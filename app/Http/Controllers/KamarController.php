<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\KamarRequest;
use App\Models\Kamar;
use App\Models\Kost;
use App\Models\Media;
use App\Models\TipeKamar;
use App\Services\FasilitasService;
use App\Services\FileUploadService;
use App\Services\KamarService;
use Illuminate\Http\Request;

class KamarController extends Controller
{
    public function __construct(
        protected KamarService $service,
        protected FileUploadService $fileUploadService,
        protected FasilitasService $fasilitasService
    ) {}

    public function index()
    {
        $data = $this->service->allWithRelations();
        $kostList = Kost::orderBy('name')->get(['id', 'name']);
        $filterKostId = request('kost_id');
        $filterStatus = request('status');

        return view('pages.kamar.index', compact('data', 'kostList', 'filterKostId', 'filterStatus'));
    }

    public function create()
    {
        [$kostList, $tipeList, $fasilitasList] = $this->formOptions();

        return view('pages.kamar.create', compact('kostList', 'tipeList', 'fasilitasList'))
            ->with('selectedKostId', request('kost_id'));
    }

    public function store(KamarRequest $request)
    {
        $data = $request->validated();
        $data['photo'] = $this->uploadPhoto($request);

        $this->service->create($data);

        return redirect()->route('kamar.index', ['kost_id' => $request->input('kost_id')])
            ->with('success', 'Data kamar berhasil ditambahkan!');
    }

    public function show(int $id)
    {
        $data = $this->service->findWithRelations($id);

        return view('pages.kamar.show', compact('data'));
    }

    public function edit(int $id)
    {
        $data = $this->service->findWithRelations($id);
        [$kostList, $tipeList, $fasilitasList] = $this->formOptions();

        return view('pages.kamar.edit', compact('data', 'kostList', 'tipeList', 'fasilitasList'));
    }

    public function update(KamarRequest $request, int $id)
    {
        $data = $request->validated();

        if ($photo = $this->uploadPhoto($request)) {
            $data['photo'] = $photo;
        }

        $this->service->update($id, $data);

        return redirect()->route('kamar.index', ['kost_id' => $request->input('kost_id')])
            ->with('success', 'Data kamar berhasil diperbarui!');
    }

    public function destroy(int $id)
    {
        $kamar = $this->service->find($id);

        if ($kamar->status !== Kamar::STATUS_AVAILABLE) {
            $message = 'Kamar berstatus '.ucfirst($kamar->status).' tidak dapat dihapus. Ubah statusnya menjadi Available terlebih dahulu.';

            if (request()->wantsJson()) {
                return ResponseHelper::error($message);
            }

            return back()->with('error', $message);
        }

        if ($kamar->photo) {
            $media = Media::where('path', $kamar->photo)->first();
            if ($media) {
                $this->fileUploadService->delete($media);
            }
        }

        $this->service->delete($id);

        if (request()->wantsJson()) {
            return ResponseHelper::success(null, 'Data kamar berhasil dihapus!');
        }

        return redirect()->route('kamar.index')
            ->with('success', 'Data kamar berhasil dihapus!');
    }

    private function uploadPhoto(Request $request): ?string
    {
        if (! $request->hasFile('photo')) {
            return null;
        }

        $media = $this->fileUploadService->upload($request->file('photo'), 'kamar', 'public', [
            'width' => 800,
            'height' => 600,
            'crop' => true,
        ]);

        return $media->path;
    }

    private function formOptions(): array
    {
        $kostList = Kost::orderBy('name')->get(['id', 'name']);
        $tipeList = TipeKamar::orderBy('name')->get(['id', 'name']);
        $fasilitasList = $this->fasilitasService->all();

        return [$kostList, $tipeList, $fasilitasList];
    }
}

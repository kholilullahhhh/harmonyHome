<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\KostRequest;
use App\Models\Media;
use App\Models\User;
use App\Services\FileUploadService;
use App\Services\FasilitasService;
use App\Services\KostService;
use App\Models\Lokasi;
use Illuminate\Http\Request;

class KostController extends Controller
{
    public function __construct(
        protected KostService $service,
        protected FileUploadService $fileUploadService,
        protected FasilitasService $fasilitasService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->service->allWithRelations();

        return view('pages.kost.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        [$pemilikList, $lokasiList, $fasilitasList] = $this->formOptions();

        return view('pages.kost.create', compact('pemilikList', 'lokasiList', 'fasilitasList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KostRequest $request)
    {
        $data = $request->validated();
        $data['cover'] = $this->uploadCover($request);

        $this->service->create($data);

        return redirect()->route('kost.index')
            ->with('success', 'Data kost berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $data = $this->service->findWithRelations($id);

        return view('pages.kost.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $data = $this->service->findWithRelations($id);
        [$pemilikList, $lokasiList, $fasilitasList] = $this->formOptions();

        return view('pages.kost.edit', compact('data', 'pemilikList', 'lokasiList', 'fasilitasList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KostRequest $request, int $id)
    {
        $data = $request->validated();

        if ($cover = $this->uploadCover($request)) {
            $data['cover'] = $cover;
        }

        $this->service->update($id, $data);

        return redirect()->route('kost.index')
            ->with('success', 'Data kost berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $kost = $this->service->find($id);

        if ($kost->cover) {
            $media = Media::where('path', $kost->cover)->first();
            if ($media) {
                $this->fileUploadService->delete($media);
            }
        }

        $this->service->delete($id);

        if (request()->wantsJson()) {
            return ResponseHelper::success(null, 'Data kost berhasil dihapus!');
        }

        return redirect()->route('kost.index')
            ->with('success', 'Data kost berhasil dihapus!');
    }

    private function uploadCover(Request $request): ?string
    {
        if (! $request->hasFile('cover')) {
            return null;
        }

        $media = $this->fileUploadService->upload($request->file('cover'), 'kosts', 'public', [
            'width' => 800,
            'height' => 600,
            'crop' => true,
        ]);

        return $media->path;
    }

    private function formOptions(): array
    {
        $pemilikList = User::whereHas('role', fn ($q) => $q->whereIn('slug', ['pemilik', 'admin', 'super-admin']))
            ->orderBy('name')
            ->get(['id', 'name']);
        $lokasiList = Lokasi::orderBy('name')->get(['id', 'name']);
        $fasilitasList = $this->fasilitasService->all();

        return [$pemilikList, $lokasiList, $fasilitasList];
    }
}

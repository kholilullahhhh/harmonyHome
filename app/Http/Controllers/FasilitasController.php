<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\FasilitasService;
use App\Http\Requests\FasilitasRequest;
use Illuminate\Http\Request;

class FasilitasController extends Controller
{
    public function __construct(
        protected FasilitasService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->service->all();
        return view('pages.fasilitas.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.fasilitas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FasilitasRequest $request)
    {
        $data = $request->validated();
        $this->service->create($data);

        return redirect()->route('fasilitas.index')
            ->with('success', 'Data berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $data = $this->service->find($id);
        return view('pages.fasilitas.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $data = $this->service->find($id);
        return view('pages.fasilitas.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FasilitasRequest $request, int $id)
    {
        $data = $request->validated();
        $this->service->update($id, $data);

        return redirect()->route('fasilitas.index')
            ->with('success', 'Data berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->service->delete($id);

        if (request()->wantsJson()) {
            return \App\Helpers\ResponseHelper::success(null, 'Data berhasil dihapus!');
        }

        return redirect()->route('fasilitas.index')
            ->with('success', 'Data berhasil dihapus!');
    }
}
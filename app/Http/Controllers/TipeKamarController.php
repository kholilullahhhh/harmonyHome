<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\TipeKamarService;
use App\Http\Requests\TipeKamarRequest;
use Illuminate\Http\Request;

class TipeKamarController extends Controller
{
    public function __construct(
        protected TipeKamarService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->service->all();
        return view('pages.tipe-kamar.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.tipe-kamar.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TipeKamarRequest $request)
    {
        $data = $request->validated();
        $this->service->create($data);

        return redirect()->route('tipe-kamar.index')
            ->with('success', 'Data berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $data = $this->service->find($id);
        return view('pages.tipe-kamar.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $data = $this->service->find($id);
        return view('pages.tipe-kamar.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TipeKamarRequest $request, int $id)
    {
        $data = $request->validated();
        $this->service->update($id, $data);

        return redirect()->route('tipe-kamar.index')
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

        return redirect()->route('tipe-kamar.index')
            ->with('success', 'Data berhasil dihapus!');
    }
}
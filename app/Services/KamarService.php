<?php

namespace App\Services;

use App\Models\Kamar;
use App\Repositories\KamarRepository;
use Illuminate\Support\Facades\DB;

class KamarService extends BaseService
{
    public function __construct(KamarRepository $repository)
    {
        parent::__construct($repository);
    }

    public function allWithRelations()
    {
        return Kamar::with(['kost:id,name,slug', 'tipeKamar'])
            ->when(request('kost_id'), fn ($q, $kostId) => $q->where('kost_id', $kostId))
            ->when(request('status'), fn ($q, $status) => $q->where('status', $status))
            ->orderBy('kost_id')
            ->orderBy('number')
            ->get();
    }

    public function findWithRelations(int $id)
    {
        return Kamar::with(['kost.lokasi', 'tipeKamar', 'fasilitas'])->findOrFail($id);
    }

    public function create(array $data)
    {
        $fasilitas = $this->extractFasilitas($data);

        return DB::transaction(function () use ($data, $fasilitas) {
            $kamar = parent::create($data);
            $kamar->fasilitas()->sync($fasilitas);

            return $kamar->load('fasilitas');
        });
    }

    public function update(int $id, array $data)
    {
        $fasilitas = $this->extractFasilitas($data);

        return DB::transaction(function () use ($id, $data, $fasilitas) {
            $kamar = parent::update($id, $data);
            $kamar->fasilitas()->sync($fasilitas);

            return $kamar->load('fasilitas');
        });
    }

    private function extractFasilitas(array &$data): array
    {
        $fasilitas = $data['fasilitas'] ?? [];
        unset($data['fasilitas']);

        return array_map('intval', $fasilitas);
    }
}

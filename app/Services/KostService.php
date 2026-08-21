<?php

namespace App\Services;

use App\Models\Kost;
use App\Repositories\KostRepository;
use Illuminate\Support\Str;

class KostService extends BaseService
{
    public function __construct(KostRepository $repository)
    {
        parent::__construct($repository);
    }

    public function allWithRelations()
    {
        return Kost::with(['pemilik', 'lokasi'])
            ->withCount('kamar')
            ->latest()
            ->get();
    }

    public function findWithRelations(int $id)
    {
        return Kost::with(['pemilik', 'lokasi', 'fasilitas', 'kamar.tipeKamar'])
            ->withCount('kamar')
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        $fasilitas = $this->extractFasilitas($data);
        $data['slug'] = $this->uniqueSlug($data['name']);

        $kost = parent::create($data);
        $kost->fasilitas()->sync($fasilitas);

        return $kost->load('fasilitas');
    }

    public function update(int $id, array $data)
    {
        $fasilitas = $this->extractFasilitas($data);

        $kost = parent::update($id, $data);
        $kost->fasilitas()->sync($fasilitas);

        return $kost->load('fasilitas');
    }

    private function extractFasilitas(array &$data): array
    {
        $fasilitas = $data['fasilitas'] ?? [];
        unset($data['fasilitas']);

        return array_map('intval', $fasilitas);
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Kost::where('slug', $slug)->exists()) {
            $slug = $base.'-'.++$i;
        }

        return $slug;
    }
}

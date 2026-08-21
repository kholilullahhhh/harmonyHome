<?php

namespace Database\Seeders;

use App\Models\Fasilitas;
use App\Models\Lokasi;
use App\Models\TipeKamar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $lokasi = ['Makassar', 'Gowa', 'Maros', 'Bantaeng', 'Parepare'];
        foreach ($lokasi as $nama) {
            Lokasi::updateOrCreate(
                ['slug' => Str::slug($nama)],
                ['name' => $nama, 'is_active' => true]
            );
        }

        $tipeKamar = [
            ['Standard', 'Kamar standar dengan fasilitas dasar, cocok untuk mahasiswa.'],
            ['Superior', 'Kamar lebih luas dengan kamar mandi dalam dan fasilitas lengkap.'],
            ['Deluxe', 'Kamar luas, fully furnished, cocok untuk pekerja profesional.'],
            ['VIP', 'Kamar premium dengan fasilitas terlengkap dan area privat.'],
            ['Ekonomis', 'Kamar hemat untuk penyewa dengan budget terbatas.'],
        ];
        foreach ($tipeKamar as [$nama, $deskripsi]) {
            TipeKamar::updateOrCreate(['name' => $nama], ['description' => $deskripsi]);
        }

        $fasilitas = [
            ['WiFi', 'ri-wifi-line'],
            ['AC', 'ri-windy-line'],
            ['Kamar Mandi Dalam', 'ri-showers-line'],
            ['Parkir Motor', 'ri-motorbike-line'],
            ['Parkir Mobil', 'ri-car-line'],
            ['CCTV', 'ri-camera-3-line'],
            ['Dapur Bersama', 'ri-restaurant-line'],
            ['Laundry', 'ri-t-shirt-line'],
            ['Kasur', 'ri-hotel-bed-line'],
            ['Lemari', 'ri-archive-line'],
            ['Meja Belajar', 'ri-table-line'],
            ['TV', 'ri-tv-2-line'],
            ['Listrik Termasuk', 'ri-flashlight-line'],
            ['Air Termasuk', 'ri-drop-line'],
            ['Furnished', 'ri-sofa-line'],
            ['Keamanan 24 Jam', 'ri-shield-check-line'],
        ];
        foreach ($fasilitas as [$nama, $icon]) {
            Fasilitas::updateOrCreate(
                ['name' => $nama],
                ['slug' => Str::slug($nama), 'icon' => $icon]
            );
        }

        $this->command->info('Master data (lokasi, tipe kamar, fasilitas) seeded.');
    }
}

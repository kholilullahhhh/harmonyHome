<?php

namespace Database\Seeders;

use App\Models\Fasilitas;
use App\Models\Kamar;
use App\Models\Kost;
use App\Models\Lokasi;
use App\Models\TipeKamar;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KostSeeder extends Seeder
{
    /**
     * Data kost realistis area Sulawesi Selatan.
     */
    private array $data = [
        ['Kost Puteri Melati', 'Makassar', 'Jl. Perintis Kemerdekaan KM 10', 'Tamalanrea', 'Tamalanrea', 90245, 'Kost putri dekat kampus UNHAS, lingkungan aman dan asri.', 850000, 1500000],
        ['Kost Griya Asri', 'Makassar', 'Jl. A. P. Pettarani No. 88', 'Masale', 'Rappocini', 90222, 'Kost campur strategis dekat pusat kuliner dan kantor.', 1000000, 2000000],
        ['Kost Anugrah Residence', 'Makassar', 'Jl. Boulevard Panakkukang No. 12', 'Pampang', 'Panakkukang', 90231, 'Kost modern fully furnished dengan CCTV 24 jam.', 1200000, 2500000],
        ['Kost Mahkota Putra', 'Makassar', 'Jl. Sultan Alauddin No. 45', 'Bontoala', 'Tamalate', 90214, 'Kost putra dekat kampus Universitas Muslim Indonesia.', 750000, 1300000],
        ['Kost Harmony Home', 'Makassar', 'Jl. Pengayoman No. 21', 'Tidung', 'Rappocini', 90171, 'Suasana rumah sendiri, penghuni terbatas dan terjaga.', 900000, 1800000],
        ['Kost Bunga Gowa', 'Gowa', 'Jl. Poros Malino KM 6', 'Sungguminasa', 'Somba Opu', 92111, 'Kost dekat kawasan kota baru Gowa, akses mudah ke Makassar.', 650000, 1200000],
        ['Kost Grand Pallantikang', 'Gowa', 'Jl. Dr. Ratulangi No. 30', 'Pallantikang', 'Pattallassang', 92461, 'Kost eksklusif dengan taman dan area parkir luas.', 800000, 1600000],
        ['Kost Maros Indah', 'Maros', 'Jl. Jend. Sudirman No. 17', 'Pettuadae', 'Turikale', 90511, 'Kost nyaman dekat alun-alun dan pasar raya Maros.', 600000, 1100000],
        ['Kost Bantaeng Jaya', 'Bantaeng', 'Jl. Andi Mappaodang No. 9', 'Bonto Manai', 'Bantaeng', 92411, 'Kost bersih dengan harga bersahabat untuk mahasiswa.', 550000, 1000000],
        ['Kost Parepare Central', 'Parepare', 'Jl. Jend. Ahmad Yani No. 50', 'Lompoe', 'Bacukiki', 91123, 'Kost di jantung kota Parepare, dekat UMPAR.', 700000, 1400000],
    ];

    public function run(): void
    {
        $pemilik = User::whereHas('role', fn ($q) => $q->where('slug', 'pemilik'))->orderBy('id')->get();
        $tipeKamar = TipeKamar::all()->keyBy('name');
        $fasilitasIds = Fasilitas::pluck('id')->toArray();
        $fasilitasWajib = Fasilitas::whereIn('slug', ['wifi', 'kasur', 'lemari'])->pluck('id')->toArray();

        foreach ($this->data as $i => [$nama, $lokasiNama, $alamat, $kelurahan, $kecamatan, $kodePos, $deskripsi, $hargaMin, $hargaMax]) {
            $lokasi = Lokasi::where('name', $lokasiNama)->firstOrFail();

            $kost = Kost::updateOrCreate(
                ['slug' => Str::slug($nama)],
                [
                    'user_id' => $pemilik[$i % $pemilik->count()]->id,
                    'lokasi_id' => $lokasi->id,
                    'name' => $nama,
                    'description' => $deskripsi,
                    'address' => $alamat,
                    'kelurahan' => $kelurahan,
                    'kecamatan' => $kecamatan,
                    'kode_pos' => (string) $kodePos,
                    'phone' => '0812'.rand(3000, 3999).rand(1000, 9999),
                    'latitude' => -5.147000 + (rand(-900, 900) / 10000),
                    'longitude' => 119.432000 + (rand(-900, 900) / 10000),
                    'rules' => "Tamu maksimal sampai pukul 21.00.\nDilarang membawa hewan peliharaan.\nDilarang merokok di dalam kamar.\nTagihan dibayar setiap tanggal 5.",
                    'access_hours' => '24 Jam (dengan konfirmasi satpam)',
                    'status' => 'active',
                ]
            );

            // Fasilitas kost: wajib + subset acak
            $kost->fasilitas()->sync(array_unique(array_merge(
                $fasilitasWajib,
                collect($fasilitasIds)->random(rand(4, 7))->all()
            )));

            // Kamar: 5-8 per kost
            $jumlahKamar = rand(5, 8);
            for ($n = 1; $n <= $jumlahKamar; $n++) {
                $tipe = match (true) {
                    $n <= 2 => $tipeKamar['Standard'],
                    $n <= 4 => $tipeKamar['Superior'],
                    $n === 5 => $tipeKamar['Ekonomis'],
                    $n <= 7 => $tipeKamar['Deluxe'],
                    default => $tipeKamar['VIP'],
                };

                $harga = $this->hargaPerTipe($tipe->name, $hargaMin, $hargaMax);

                Kamar::updateOrCreate(
                    ['kost_id' => $kost->id, 'number' => sprintf('A%02d', $n)],
                    [
                        'tipe_kamar_id' => $tipe->id,
                        'price_monthly' => $harga,
                        'size' => rand(3, 4).'x'.rand(3, 5),
                        'floor' => min(2, intdiv($n - 1, 4)),
                        'description' => "Kamar {$tipe->name} lantai ".min(2, intdiv($n - 1, 4)).', jendela menghadap '.($n % 2 ? 'depan' : 'belakang').'.',
                        'status' => 'available',
                    ]
                )->fasilitas()->sync(array_unique(array_merge(
                    $fasilitasWajib,
                    collect($fasilitasIds)->random(rand(2, 5))->all()
                )));
            }

            // Sebagian kecil kost punya kamar dalam perbaikan
            if (fake()->boolean(35)) {
                $kost->kamar()->inRandomOrder()->take(rand(1, 2))->update(['status' => Kamar::STATUS_MAINTENANCE]);
            }
        }

        $this->command->info('Seeded '.Kost::count().' kost dengan total '.Kamar::count().' kamar.');
    }

    private function hargaPerTipe(string $tipe, int $min, int $max): int
    {
        $porsi = match ($tipe) {
            'Ekonomis' => 0.0,
            'Standard' => 0.25,
            'Superior' => 0.5,
            'Deluxe' => 0.75,
            'VIP' => 1.0,
            default => 0.5,
        };

        $harga = $min + (($max - $min) * $porsi);

        return (int) (round($harga / 50000) * 50000); // bulatkan ke 50rb
    }
}

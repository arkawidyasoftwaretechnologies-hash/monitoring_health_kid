<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummyWhoReferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dummy data untuk uji coba Z-score
        // Misalnya: WAZ Laki-laki usia 9 bulan
        $data = [
            ['indeks' => 'waz', 'jenis_kelamin' => 'L', 'usia_bulan' => 9, 'L' => -0.1601, 'M' => 8.9481, 'S' => 0.11166],
            ['indeks' => 'haz', 'jenis_kelamin' => 'L', 'usia_bulan' => 9, 'L' => 1, 'M' => 71.9686, 'S' => 0.03819],
            ['indeks' => 'whz', 'jenis_kelamin' => 'L', 'usia_bulan' => 9, 'L' => -0.3521, 'M' => 8.9481, 'S' => 0.08227],
            // Data dummy tambahan untuk BMIZ dan HCFA
            ['indeks' => 'bmiz', 'jenis_kelamin' => 'L', 'usia_bulan' => 9, 'L' => -0.063, 'M' => 17.3, 'S' => 0.083],
            ['indeks' => 'hcfa', 'jenis_kelamin' => 'L', 'usia_bulan' => 9, 'L' => 1, 'M' => 45.4, 'S' => 0.030],
        ];

        foreach ($data as $row) {
            \App\Models\WhoGrowthReference::create($row);
        }
    }
}

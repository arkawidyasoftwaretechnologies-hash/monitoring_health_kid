<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WhoWhzReferenceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('who_whz_references')->truncate();
        
        $data = [];
        $now = Carbon::now();
        
        foreach (['L', 'P'] as $jk) {
            foreach (['panjang', 'tinggi'] as $tipe) {
                // WHO usually measures 'panjang' up to 110cm, and 'tinggi' starting from 65cm. 
                // We'll just generate the full range 45 to 120 for both for demo purposes, 
                // but shift the M slightly for standing (-0.7cm equivalent impact).
                
                for ($ht = 45.0; $ht <= 120.0; $ht += 0.5) {
                    $ht_m = $ht / 100;
                    $bmi = 17.5 - ($ht_m * 2); 
                    $M = $bmi * $ht_m * $ht_m;
                    
                    if ($jk === 'L') {
                        $M *= 1.02;
                    }
                    
                    if ($tipe === 'tinggi') {
                        $M *= 0.99; // Slightly lighter for same numerical height standing
                    }
                    
                    $data[] = [
                        'jenis_kelamin' => $jk,
                        'panjang_tinggi' => $ht,
                        'tipe_ukur' => $tipe,
                        'L' => 1.0000,
                        'M' => round($M, 4),
                        'S' => 0.0800,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    
                    if (count($data) >= 500) {
                        DB::table('who_whz_references')->insert($data);
                        $data = [];
                    }
                }
            }
        }
        
        if (count($data) > 0) {
            DB::table('who_whz_references')->insert($data);
        }
    }
}

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
        
        $now = Carbon::now();
        $basePath = base_path('database/data/who_whz');
        
        $files = [
            ['path' => "$basePath/wfl_boys.csv", 'jk' => 'L', 'tipe' => 'panjang'],
            ['path' => "$basePath/wfl_girls.csv", 'jk' => 'P', 'tipe' => 'panjang'],
            ['path' => "$basePath/wfh_boys.csv", 'jk' => 'L', 'tipe' => 'tinggi'],
            ['path' => "$basePath/wfh_girls.csv", 'jk' => 'P', 'tipe' => 'tinggi'],
        ];

        foreach ($files as $fileMeta) {
            $path = $fileMeta['path'];
            $jk = $fileMeta['jk'];
            $tipe = $fileMeta['tipe'];

            if (!file_exists($path)) {
                $this->command->warn("File not found: $path");
                continue;
            }

            $data = [];
            $header = true;
            if (($handle = fopen($path, "r")) !== FALSE) {
                while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if ($header) {
                        $header = false;
                        continue;
                    }
                    
                    if (count($row) >= 4) {
                        $data[] = [
                            'jenis_kelamin' => $jk,
                            'panjang_tinggi' => (float)$row[0],
                            'tipe_ukur' => $tipe,
                            'L' => (float)$row[1],
                            'M' => (float)$row[2],
                            'S' => (float)$row[3],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];

                        if (count($data) >= 500) {
                            DB::table('who_whz_references')->insert($data);
                            $data = [];
                        }
                    }
                }
                fclose($handle);
                
                if (count($data) > 0) {
                    DB::table('who_whz_references')->insert($data);
                }
            }
        }
    }
}

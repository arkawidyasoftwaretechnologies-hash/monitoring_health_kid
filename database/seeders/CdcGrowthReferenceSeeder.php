<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CdcGrowthReferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $urls = [
            'waz' => [
                'https://www.cdc.gov/growthcharts/data/zscore/wtageinf.csv', // < 36 months
                'https://www.cdc.gov/growthcharts/data/zscore/wtage.csv',    // 2-20 years
            ],
            'haz' => [
                'https://www.cdc.gov/growthcharts/data/zscore/lenageinf.csv', // < 36 months
                'https://www.cdc.gov/growthcharts/data/zscore/statage.csv',   // 2-20 years
            ],
            'bmiz' => [
                'https://www.cdc.gov/growthcharts/data/zscore/bmiagerev.csv', // 2-20 years
            ]
        ];

        $this->command->info('Downloading and importing CDC 2000 Growth Reference Data...');

        foreach ($urls as $indeks => $links) {
            foreach ($links as $url) {
                $this->command->info("Processing {$indeks} from {$url}...");
                
                try {
                    $response = \Illuminate\Support\Facades\Http::timeout(30)->get($url);
                    if (!$response->successful()) {
                        $this->command->error("Failed to download {$url}: HTTP " . $response->status());
                        continue;
                    }
                    $csvData = $response->body();
                } catch (\Exception $e) {
                    $this->command->error("Failed to download {$url}: " . $e->getMessage());
                    continue;
                }

                $lines = explode("\n", trim($csvData));
                $header = str_getcsv(array_shift($lines));
                
                // Hapus karakter BOM (Byte Order Mark) jika ada (sering muncul di CSV dari CDC)
                $header = array_map(function($val) {
                    return trim(preg_replace('/[\xef\xbb\xbf]/', '', $val));
                }, $header);
                
                // Cari index kolom
                $idxSex = array_search('Sex', $header);
                $idxAgemos = array_search('Agemos', $header);
                $idxL = array_search('L', $header);
                $idxM = array_search('M', $header);
                $idxS = array_search('S', $header);

                if ($idxSex === false || $idxAgemos === false || $idxL === false) {
                    $this->command->error("CSV format invalid for {$url}");
                    continue;
                }

                $insertData = [];
                foreach ($lines as $line) {
                    $row = str_getcsv($line);
                    if (count($row) < 5) continue;
                    
                    $sex = $row[$idxSex] == '1' ? 'L' : 'P';
                    $age = (float) $row[$idxAgemos];
                    $l = (float) $row[$idxL];
                    $m = (float) $row[$idxM];
                    $s = (float) $row[$idxS];

                    // Abaikan baris kosong atau tidak valid (M=0 atau S=0 akan memicu DivisionByZeroError)
                    if ($m == 0 || $s == 0) continue;

                    // Agar proses lebih cepat, kita gunakan insert bulk atau insert ignore
                    // Jika ada konflik usia (misal bulan 24 dari file inf dan file 2-20yrs), kita timpa.
                    \App\Models\CdcGrowthReference::updateOrCreate(
                        [
                            'indeks' => $indeks,
                            'jenis_kelamin' => $sex,
                            'usia_bulan' => $age,
                        ],
                        [
                            'L' => $l,
                            'M' => $m,
                            'S' => $s,
                        ]
                    );
                }
            }
        }
        
        $this->command->info('CDC 2000 Data imported successfully!');
    }
}

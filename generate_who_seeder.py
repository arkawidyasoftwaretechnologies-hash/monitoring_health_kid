import os
import requests
import pandas as pd
import io

urls = {
    'waz_boys': ('waz', 'L', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/weight-for-age/wfa_boys_0-to-5-years_zscores.xlsx?sfvrsn=222bebf4_7'),
    'waz_girls': ('waz', 'P', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/weight-for-age/wfa_girls_0-to-5-years_zscores.xlsx?sfvrsn=e34dcc4c_9'),
    'haz_boys_0_2': ('haz', 'L', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/length-height-for-age/lhfa_boys_0-to-2-years_zscores.xlsx?sfvrsn=30e044c_9'),
    'haz_boys_2_5': ('haz', 'L', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/length-height-for-age/lhfa_boys_2-to-5-years_zscores.xlsx?sfvrsn=17e5ad91_9'),
    'haz_girls_0_2': ('haz', 'P', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/length-height-for-age/lhfa_girls_0-to-2-years_zscores.xlsx?sfvrsn=e9e66a95_11'),
    'haz_girls_2_5': ('haz', 'P', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/length-height-for-age/lhfa_girls_2-to-5-years_zscores.xlsx?sfvrsn=2ec187b9_11'),
    'bmiz_boys': ('bmiz', 'L', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/bmi-for-age/bfa_boys_0-to-5-years_zscores.xlsx?sfvrsn=cd1712dc_7'),
    'bmiz_girls': ('bmiz', 'P', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/bmi-for-age/bfa_girls_0-to-5-years_zscores.xlsx?sfvrsn=68a41764_9'),
    'hcfa_boys': ('hcfa', 'L', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/head-circumference-for-age/hcfa_boys_0-to-5-years_zscores.xlsx?sfvrsn=22da630c_7'),
    'hcfa_girls': ('hcfa', 'P', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/head-circumference-for-age/hcfa_girls_0-to-5-years_zscores.xlsx?sfvrsn=dd503309_7')
}

records = []

headers = {
    'User-Agent': 'Mozilla/5.0'
}

for key, (indeks, jk, url) in urls.items():
    print(f"Downloading {key}...")
    res = requests.get(url, headers=headers)
    if res.status_code != 200:
        print(f"FAILED to download {key} (Status {res.status_code})")
        # Try alternate URL for BMI since sometimes they use bfa instead of bmi
        if 'bfa' in url:
            alt_url = url.replace('bfa_', 'bmi_')
            print(f"Trying alt URL: {alt_url}")
            res = requests.get(alt_url, headers=headers)
            if res.status_code != 200:
                print("Alt URL failed too.")
                continue

    try:
        df = pd.read_excel(io.BytesIO(res.content))
        # Keep Month, L, M, S
        df = df.dropna(subset=['Month', 'L', 'M', 'S'])
        
        for index, row in df.iterrows():
            month = int(row['Month'])
            
            # Since haz is split 0-2 and 2-5, we don't want duplicate month 24
            if key in ['haz_boys_2_5', 'haz_girls_2_5'] and month == 24:
                continue
                
            records.append({
                'indeks': indeks,
                'jenis_kelamin': jk,
                'usia_bulan': month,
                'L': round(row['L'], 4),
                'M': round(row['M'], 4),
                'S': round(row['S'], 5)
            })
    except Exception as e:
        print(f"Error parsing {key}: {e}")

# Format records into PHP array
php_code = """<?php

namespace Database\\Seeders;

use Illuminate\\Database\\Seeder;
use Illuminate\\Support\\Facades\\DB;

class FullWhoReferenceSeeder extends Seeder
{
    public function run()
    {
        DB::table('who_growth_references')->truncate();
        $data = [
"""

for r in records:
    php_code += f"            ['indeks' => '{r['indeks']}', 'jenis_kelamin' => '{r['jenis_kelamin']}', 'usia_bulan' => {r['usia_bulan']}, 'L' => {r['L']}, 'M' => {r['M']}, 'S' => {r['S']}],\n"

php_code += """        ];

        foreach (array_chunk($data, 100) as $chunk) {
            DB::table('who_growth_references')->insert($chunk);
        }
    }
}
"""

with open('database/seeders/FullWhoReferenceSeeder.php', 'w') as f:
    f.write(php_code)

print("Generated database/seeders/FullWhoReferenceSeeder.php")

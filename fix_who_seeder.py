import requests
import pandas as pd
import io

urls = {
    'waz_boys': ('waz', 'L', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/weight-for-age/wfa_boys_0-to-5-years_zscores.xlsx?sfvrsn=97a05331_9'),
    'waz_girls': ('waz', 'P', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/weight-for-age/wfa_girls_0-to-5-years_zscores.xlsx?sfvrsn=4c03b8db_7'),
    'haz_boys_0_2': ('haz', 'L', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/length-height-for-age/lhfa_boys_0-to-2-years_zscores.xlsx?sfvrsn=30e044c_9'),
    'haz_boys_2_5': ('haz', 'L', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/length-height-for-age/lhfa_boys_2-to-5-years_zscores.xlsx?sfvrsn=17e5ad91_9'),
    'haz_girls_0_2': ('haz', 'P', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/length-height-for-age/lhfa_girls_0-to-2-years_zscores.xlsx?sfvrsn=e9e66a95_11'),
    'haz_girls_2_5': ('haz', 'P', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/length-height-for-age/lhfa_girls_2-to-5-years_zscores.xlsx?sfvrsn=2ec187b9_11'),
    'hcfa_boys': ('hcfa', 'L', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/head-circumference-for-age/hcfa-boys-0-5-zscores.xlsx?sfvrsn=adf57aa4_8'),
    'hcfa_girls': ('hcfa', 'P', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/head-circumference-for-age/hcfa-girls-0-5-zscores.xlsx?sfvrsn=8f959f88_6'),
    'bmiz_boys': ('bmiz', 'L', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/body-mass-index-for-age/bmi_boys_0-to-5-years_zscores.xlsx?sfvrsn=9b60b7eb_10'),
    'bmiz_girls': ('bmiz', 'P', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/body-mass-index-for-age/bmi_girls_0-to-5-years_zscores.xlsx?sfvrsn=24b3b19_10')
}

headers = {'User-Agent': 'Mozilla/5.0'}
records = {}

for key, (indeks, jk, url) in urls.items():
    print(f"Downloading {key}...")
    res = requests.get(url, headers=headers)
    if res.status_code == 200:
        try:
            df = pd.read_excel(io.BytesIO(res.content))
            df.columns = df.columns.str.strip()
            df = df.dropna(subset=['Month', 'L', 'M', 'S'])
            for index, row in df.iterrows():
                month = int(row['Month'])
                if month > 60:
                    continue
                if key in ['haz_boys_2_5', 'haz_girls_2_5'] and month == 24:
                    continue
                records[(indeks, jk, month)] = {
                    'L': round(row['L'], 4),
                    'M': round(row['M'], 4),
                    'S': round(row['S'], 5)
                }
        except Exception as e:
            print(f"Error parsing {key}: {e}")
    else:
        print(f"Failed to download {key}, status code: {res.status_code}")

new_data_lines = []

for (indeks, jk, month), r in sorted(records.items()):
    new_data_lines.append(f"            ['indeks' => '{indeks}', 'jenis_kelamin' => '{jk}', 'usia_bulan' => {month}, 'L' => {r['L']}, 'M' => {r['M']}, 'S' => {r['S']}],")

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
php_code += "\n".join(new_data_lines)
php_code += """
        ];

        foreach (array_chunk($data, 100) as $chunk) {
            DB::table('who_growth_references')->insert($chunk);
        }
    }
}
"""

with open('database/seeders/FullWhoReferenceSeeder.php', 'w') as f:
    f.write(php_code)

print("Updated database/seeders/FullWhoReferenceSeeder.php completely!")

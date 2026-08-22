import requests
import pandas as pd
import io
import re

urls = {
    'haz_boys_2_5': ('haz', 'L', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/length-height-for-age/lhfa_boys_2-to-5-years_zscores.xlsx'),
    'haz_girls_2_5': ('haz', 'P', 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/length-height-for-age/lhfa_girls_2-to-5-years_zscores.xlsx')
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
                if month > 60 or month <= 24: # we only want > 24
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

with open('database/seeders/FullWhoReferenceSeeder.php', 'r') as f:
    content = f.read()

# Replace the closing of the array to append
append_str = "\n".join(new_data_lines) + "\n        ];"
content = content.replace("        ];", append_str, 1) # Only replace the first occurrence (the array closing)

with open('database/seeders/FullWhoReferenceSeeder.php', 'w') as f:
    f.write(content)

print("Appended HAZ 25-60 to database/seeders/FullWhoReferenceSeeder.php!")

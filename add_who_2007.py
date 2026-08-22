import requests
import pandas as pd
import io

urls = {
    'haz_boys_5_19': ('haz', 'L', 'https://cdn.who.int/media/docs/default-source/child-growth/growth-reference-5-19-years/height-for-age-(5-19-years)/hfa-boys-z-who-2007-exp.xlsx?sfvrsn=7fa263d_2'),
    'haz_girls_5_19': ('haz', 'P', 'https://cdn.who.int/media/docs/default-source/child-growth/growth-reference-5-19-years/height-for-age-(5-19-years)/hfa-girls-z-who-2007-exp.xlsx?sfvrsn=79d310ee_2'),
    'bmiz_boys_5_19': ('bmiz', 'L', 'https://cdn.who.int/media/docs/default-source/child-growth/growth-reference-5-19-years/bmi-for-age-(5-19-years)/bmi-boys-z-who-2007-exp.xlsx?sfvrsn=a84bca93_2'),
    'bmiz_girls_5_19': ('bmiz', 'P', 'https://cdn.who.int/media/docs/default-source/child-growth/growth-reference-5-19-years/bmi-for-age-(5-19-years)/bmi-girls-z-who-2007-exp.xlsx?sfvrsn=79222875_2')
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
            # Month might be called 'Month'
            df = df.dropna(subset=['Month', 'L', 'M', 'S'])
            for index, row in df.iterrows():
                month = int(row['Month'])
                if month <= 60 or month > 228:
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

append_str = "\n".join(new_data_lines) + "\n        ];"
content = content.replace("        ];", append_str, 1)

with open('database/seeders/FullWhoReferenceSeeder.php', 'w') as f:
    f.write(content)

print(f"Appended {len(new_data_lines)} records to database/seeders/FullWhoReferenceSeeder.php!")

import os
import requests
import pandas as pd

urls = {
    'wfl_boys': 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/weight-for-length-height/wfl_boys_0-to-2-years_zscores.xlsx?sfvrsn=e27a9da3_7',
    'wfl_girls': 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/weight-for-length-height/wfl_girls_0-to-2-years_zscores.xlsx?sfvrsn=288bc4e4_7',
    'wfh_boys': 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/weight-for-length-height/wfh_boys_2-to-5-years_zscores.xlsx?sfvrsn=202c0545_7',
    'wfh_girls': 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/weight-for-length-height/wfh_girls_2-to-5-years_zscores.xlsx?sfvrsn=4d66af6a_7'
}

data_dir = 'database/data/who_whz'
os.makedirs(data_dir, exist_ok=True)

for key, url in urls.items():
    print(f"Downloading {key}...")
    xlsx_path = os.path.join(data_dir, f"{key}.xlsx")
    csv_path = os.path.join(data_dir, f"{key}.csv")
    
    # Download file
    headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
    }
    response = requests.get(url, headers=headers)
    with open(xlsx_path, 'wb') as f:
        f.write(response.content)
        
    print(f"Converting {key} to CSV...")
    # WHO Excel files usually have columns: Length/Height, L, M, S, SD3neg, SD2neg, SD1neg, SD0, SD1, SD2, SD3
    # We just need Length/Height, L, M, S
    df = pd.read_excel(xlsx_path)
    
    # Let's standardize the length/height column name
    # Depending on the file, it could be 'Length' or 'Height'
    col_to_keep = []
    for col in df.columns:
        if 'Length' in col or 'Height' in col or col == 'L' or col == 'M' or col == 'S':
            col_to_keep.append(col)
            
    df = df[col_to_keep]
    # Rename length/height to 'length_height'
    df = df.rename(columns={col_to_keep[0]: 'length_height'})
    
    # Drop rows with NaN
    df = df.dropna(subset=['length_height', 'L', 'M', 'S'])
    
    df.to_csv(csv_path, index=False)
    print(f"Saved {csv_path}")
    
    # Optional: remove xlsx
    os.remove(xlsx_path)

print("Done!")

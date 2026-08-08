import requests
import pandas as pd
import io

url = 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/length-height-for-age/lhfa_boys_0-to-2-years_zscores.xlsx?sfvrsn=30e044c_9'
headers = {
    'User-Agent': 'Mozilla/5.0'
}
res = requests.get(url, headers=headers)
df = pd.read_excel(io.BytesIO(res.content))
print(df.head())
print("Unique L values:", df['L'].unique())

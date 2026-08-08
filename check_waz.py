import requests
import pandas as pd
import io

url = 'https://cdn.who.int/media/docs/default-source/child-growth/child-growth-standards/indicators/weight-for-age/wfa_boys_0-to-5-years_zscores.xlsx?sfvrsn=222bebf4_7'
headers = {
    'User-Agent': 'Mozilla/5.0'
}
res = requests.get(url, headers=headers)
df = pd.read_excel(io.BytesIO(res.content))
print(df.head())

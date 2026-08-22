import requests
import io
import pandas as pd

urls = {
    'bmi_boys_5_19': 'https://cdn.who.int/media/docs/default-source/child-growth/growth-reference-5-19-years/bmi-for-age-(5-19-years)/bmi_boys_5-19years_z.xlsx',
    'hfa_boys_5_19': 'https://cdn.who.int/media/docs/default-source/child-growth/growth-reference-5-19-years/height-for-age-(5-19-years)/hfa_boys_5-19years_z.xlsx',
    'wfa_boys_5_10': 'https://cdn.who.int/media/docs/default-source/child-growth/growth-reference-5-19-years/weight-for-age-(5-10-years)/wfa_boys_5-10years_z.xlsx'
}

headers = {'User-Agent': 'Mozilla/5.0'}

for key, url in urls.items():
    res = requests.get(url, headers=headers)
    print(f"{key}: {res.status_code}")

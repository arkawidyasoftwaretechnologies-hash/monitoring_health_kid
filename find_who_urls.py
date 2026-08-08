import requests
from bs4 import BeautifulSoup
import re

url = 'https://www.who.int/tools/child-growth-standards/standards/body-mass-index-for-age-bmi-for-age'
res = requests.get(url, headers={'User-Agent': 'Mozilla/5.0'})
soup = BeautifulSoup(res.content, 'html.parser')
for a in soup.find_all('a', href=True):
    if '.xlsx' in a['href']:
        print(a['href'])

import requests

auth=('rzp_live_RrvL9p6OF2hGP1', 'OK5bMD066Z4PCeLdc4YxoW8U')
url='https://api.razorpay.com/v1/payments?count=100&skip=0'

res=requests.get(url, auth=(auth))
all_res=[]
for i in range(21):
    url=f'https://api.razorpay.com/v1/payments?count=100&skip={i*100}'
    res=requests.get(url, auth=(auth))
    all_res.append(res.json())
    print(i)

# pay_Rv5GX3ymtJSxqu 264

a={'items':[]}

for i in all_res:
    for j in i['items']:
        a['items'].append(j)

import json
f=open('all_transaction.json','w')
json.dump(a, f)
f.close()
#!/usr/bin/python

import requests
import json
import csv
import sys

'''
curl  https://accounts.zoho.com/oauth/v2/token \
        -X POST \
        -d "client_id= " \
        -d "client_secret= "\
        -d "code= "\
        -d "grant_type=authorization_code" \

revoke refresh token    POST https://accounts.zoho.com/oauth/v2/token/revoke?token={refresh_token}
curl https://accounts.zoho.com/oauth/v2/token/revoke?token= \
    -X POST

https://api-console.zoho.com/client/
https://www.site24x7.com/help/api/#api-versioning
api methods names:
 oauthscope : Site24x7.Operations.Create
               Site24x7.Admin.Read
'''

def get_urls_from_csv(path):
    rows = []
    urls = []
    with open(path, newline='', encoding='utf-16') as csvfile:
        csv_reader = csv.reader(csvfile, delimiter='\t', quotechar='|')
        for row in csv_reader:
            rows.append(row[0].split(','))
    for rows_i in rows[1:]:
        urls.append(rows_i[1])
    return urls


def get_new_token():
    id = ''
    secret = ''
    refresh_token = ''
    payload = {'client_id': id, 'client_secret': secret, 'refresh_token': refresh_token, 'grant_type': 'refresh_token'}
    reply = requests.post(url_get_token, payload)
    data = reply.json()
    access_token = data["access_token"]
    return access_token


'''
Monitor Group Id's
World wide location - location_profile_id - 
Default threshold_profile_id - 
Default notification_profile_id - 
Default user_group_ids - 
'''

# Variables definition
notification_profile_id = ""
threshold_profile_id = ""
location_profile_id = ""
user_group_ids = ""
type = "URL"
timeout = 45
http_method = "G"
check_frequency = "5"
monitor_group_id = ""
url_get_token = "https://accounts.zoho.com/oauth/v2/token"
url_monitors = "https://www.site24x7.com/api/monitors"


# Check if access token has been obtained
api_authtoken = get_new_token()

if api_authtoken is None:
    print("Cannot obtain site24x7 access token to use Site24x7 api")
    exit(1)

HEADERS = {'Accept': 'application/json; version=2.1', 'Authorization': 'Zoho-oauthtoken' + ' ' + api_authtoken}

monitor_names = [""]

for monitor in monitor_names:

    payload = {
          "display_name": monitor,
          "type": "URL",
          "website": "https://" + monitor,
          "check_frequency": check_frequency,
          "timeout": timeout,
          "http_method": "G",
          "location_profile_id": location_profile_id,
          "notification_profile_id": notification_profile_id,
          "threshold_profile_id": threshold_profile_id,
          "user_group_ids": [user_group_ids],
          "monitor_groups": [monitor_group_id]
    }

    post_monitor = requests.post(url_monitors, headers=HEADERS, data=json.dumps(payload))
    print(post_monitor)
    print(monitor)
else:
    print("Finally finished!")

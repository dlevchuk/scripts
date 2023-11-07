from telethon import TelegramClient, sync
import time

api_id = ""
api_hash = ""

client = TelegramClient("ava", "", "")
client.start()

from telethon.tl.functions.photos import UploadProfilePhotoRequest, DeletePhotosRequest
from datetime import datetime

def convert_time_to_string(dt):
    return f"{dt.hour}:{dt.minute:02}"

def time_has_changed(prev_time):
    return convert_time_to_string(datetime.now()) != prev_time


prev_update_time = ""

while True:
    if time_has_changed(prev_update_time):
        prev_update_time = convert_time_to_string(datetime.now())
        client(DeletePhotosRequest(client.get_profile_photos('me')))
        file = client.upload_file(f"//telegram_ava/time_images/{prev_update_time}.jpg")
        client(UploadProfilePhotoRequest(file))
    time.sleep(1)



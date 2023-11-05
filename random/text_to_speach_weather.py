from gtts import gTTS
from playsound import playsound
import requests
import inflect
from datetime import datetime


api_id = ""
city_id = 

#int to string
p = inflect.engine()
now = datetime.now()
string_to_say = 'today is the ' + p.number_to_words(now.strftime("%d")) + ' of ' + now.strftime("%B") + ', time ' + p.number_to_words(now.strftime("%H")) + ' hours, ' + p.number_to_words(now.strftime("%M")) + ' minutes,'


try:
    res = requests.get("http://api.openweathermap.org/data/2.5/weather",
                 params={'id': city_id, 'units': 'metric', 'lang': 'en', 'APPID': api_id})
    data = res.json()


    tts = gTTS(string_to_say + 'weather today, ' + data['weather'][0]['description'] + ',temperature, ' + p.number_to_words(data['main']['temp']), 'en')
    tts.save('hello.mp3')
    playsound('hello.mp3')

except Exception as e:
    print("Exception (weather):", e)
    pass
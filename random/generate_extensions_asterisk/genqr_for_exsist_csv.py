"""Generate qr-code for exsisting extensions"""

import pyqrcode 
from pyqrcode import QRCode 
import sys
import csv

server = ""


with open('/home/last/Scripts/generate_extensions_asterisk/sov_16d.csv', newline='') as csvfile:
     reader = csv.DictReader(csvfile)
     for row in reader:
         ext = row['extension']
         secret = row['secret']
         string = '<?xml version="1.0" encoding="utf-8"?><AccountConfig version="1"><Account><RegisterServer>'+server+'</RegisterServer><UserID>'+ext+'</UserID><AuthID>'+ext+'</AuthID><AuthPass>'+secret+'</AuthPass><AccountName>'+ext+'</AccountName><DisplayName>'+ext+'</DisplayName></Account></AccountConfig>'
         qrcode = pyqrcode.create(string, error='L', version=27, mode='binary')
         qrcode.png('Account_'+ext+'.png', scale=2, module_color=[0, 0, 0, 128], background=[255, 255, 255])
"""Generate qr-code,csv for import to sip-server"""

import csv
import random
import pyqrcode 
from pyqrcode import QRCode 
from fpdf import FPDF
import argparse
import sys
from pathlib import Path

class CustomFormatter(argparse.RawDescriptionHelpFormatter,
                      argparse.ArgumentDefaultsHelpFormatter):
    pass

def parse_args(args=sys.argv[1:]):
    """Parse arguments."""
    parser = argparse.ArgumentParser(
        description=sys.modules[__name__].__doc__,
        formatter_class=CustomFormatter)
   
    parser.add_argument("number",type=int, help="number of extensions")
    parser.add_argument("FILENAME", help="name of generated csv file")
    parser.add_argument("server", help="ip:port or domain name:port")

    return parser.parse_args(args)

options = parse_args()

#FILENAME = "chernoe_more"
#server = '78.26.150.10:41865'
headers = ['extension', 'password', 'name', 'voicemail', 'ringtimer', 'noanswer', 'recording', 'outboundcid', 'sipname', 'noanswer_cid', 'busy_cid', 'chanunavail_cid', 'noanswer_dest', 'busy_dest', 'chanunavail_dest', 'mohclass', 'id', 'tech', 'dial', 'devicetype', 'user', 'description', 'emergency_cid', 'hint_override', 'recording_in_external', 'recording_out_external', 'recording_in_internal', 'recording_out_internal', 'recording_ondemand', 'recording_priority', 'answermode', 'intercom', 'cid_masquerade', 'concurrency_limit', 'accountcode', 'allow', 'avpf', 'callerid', 'canreinvite', 'context', 'defaultuser', 'deny', 'disallow', 'dtmfmode', 'encryption', 'force_avp', 'host', 'icesupport', 'mailbox', 'namedcallgroup', 'namedpickupgroup', 'nat', 'permit', 'port', 'qualify', 'qualifyfreq', 'rtcp_mux', 'secret', 'sendrpid', 'sessiontimers', 'sipdriver', 'transport', 'trustrpid', 'type', 'videosupport', 'vmexten', 'callwaiting_enable', 'findmefollow_strategy', 'findmefollow_grptime', 'findmefollow_grppre', 'findmefollow_grplist', 'findmefollow_annmsg_id', 'findmefollow_postdest', 'findmefollow_dring', 'findmefollow_needsconf', 'findmefollow_remotealert_id', 'findmefollow_toolate_id', 'findmefollow_ringing', 'findmefollow_pre_ring', 'findmefollow_voicemail', 'findmefollow_calendar_id', 'findmefollow_calendar_match', 'findmefollow_changecid', 'findmefollow_fixedcid', 'findmefollow_enabled', 'languages_language', 'voicemail_enable', 'voicemail_vmpwd', 'voicemail_email', 'voicemail_pager', 'voicemail_options', 'voicemail_same_exten', 'disable_star_voicemail', 'vmx_unavail_enabled', 'vmx_busy_enabled', 'vmx_temp_enabled', 'vmx_play_instructions', 'vmx_option_0_number', 'vmx_option_1_number', 'vmx_option_2_number']

extensions_pdf = []
extensions = []
a = []
j = []
for i in range(1, options.number+1):
    extensions.append(format(i, '02'))
    a.append(str(format(i, '02')) + '-' + str(i*100+1) +  '-' + str(i*100+2) +  '-' + str(i*100+3))
    extensions_pdf.append(format(i, '02'))
#    j.append(str(format(i, '02')) + '-' + str(i*1000+1) +  '-' + str(i*1000+2) +  '-' + str(i*1000+3))
    for j in range(i*100+1, i*100+4):
        extensions.append(j)
        a.append(None)
        extensions_pdf.append(str(j))

password = [None]*len(extensions)
name = extensions
voicemail = ["novm"]*len(extensions)
ringtimer = [60]*len(extensions)
noanswer = [None]*len(extensions)
recording = [None]*len(extensions)
outboundcid = extensions
sipname = [None]*len(extensions)
noanswer_cid = [None]*len(extensions)
busy_cid = [None]*len(extensions)
chanunavail_cid = [None]*len(extensions)
noanswer_dest = [None]*len(extensions)
busy_dest = [None]*len(extensions)
chanunavail_dest = [None]*len(extensions)
mohclass = ["default"]*len(extensions)
id = extensions
tech = ["sip"]*len(extensions)

dial = ["{}{}".format("SIP/",i) for i in extensions]

devicetype = ["fixed"]*len(extensions)
user = extensions
description = extensions
emergency_cid = [None]*len(extensions)
hint_override = [None]*len(extensions)
recording_in_external = ["dontcare"]*len(extensions)
recording_out_external = ["dontcare"]*len(extensions)
recording_in_internal = ["dontcare"]*len(extensions)
recording_out_internal = ["dontcare"]*len(extensions)
recording_ondemand = ["disabled"]*len(extensions)
recording_priority = [0]*len(extensions)
answermode = ["disabled"]*len(extensions)
intercom = ["enabled"]*len(extensions)
cid_masquerade = extensions
concurrency_limit = [3]*len(extensions)
accountcode = [None]*len(extensions)
allow = [None]*len(extensions)
avpf = [None]*len(extensions)

callerid = [str(x) + str(' <') + str(x) + str('>') for x in extensions]
#output = [str(x) + str(' <') + str(x) + str('>') for x in extensions]
#print(output)

canreinvite = [None]*len(extensions)
context = ["from-internal"]*len(extensions)
defaultuser = [None]*len(extensions)
deny = [None]*len(extensions)
disallow = [None]*len(extensions)
dtmfmode = ["rfc2833"]*len(extensions)
encryption = ["no"]*len(extensions)
force_avp = ["no"]*len(extensions)
host = ["dynamic"]*len(extensions)
icesupport = ["no"]*len(extensions)
mailbox = [None]*len(extensions)
namedcallgroup = [None]*len(extensions)
namedpickupgroup = [None]*len(extensions)
nat = ["yes"]*len(extensions)
permit = ["0.0.0.0/0.0.0.0"]*len(extensions)
port = [5060]*len(extensions)
qualify = ["yes"]*len(extensions) 
qualifyfreq = [None]*len(extensions)
rtcp_mux = [None]*len(extensions)

chars = '+-/*!&$#?=@<>abcdefghijklnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
number = len(extensions)
length = int(8)
secret = []
for n in range(number):
    secret1 =''
    for i in range(length):
        secret1 += random.choice(chars)
    secret.append(secret1)

sendrpid = ["pai"]*len(extensions)
sessiontimers = ["accept"]*len(extensions)
sipdriver = ["chan_sip"]*len(extensions)
transport = [None]*len(extensions)
trustrpid = [None]*len(extensions)
type = ["friend"]*len(extensions)
videosupport = ["yes"]*len(extensions)
vmexten = [None]*len(extensions)
callwaiting_enable = ["ENABLED"]*len(extensions)
findmefollow_strategy = ["ringallv2-prim"]*len(extensions)
findmefollow_grptime = [60]*len(extensions)   #The time, in seconds, during which the phones will ring. For all types of series lookup strategies, this ringing time is repeated for all phones that are included in the series.
findmefollow_grppre = [None]*len(extensions)

findmefollow_grplist = a

findmefollow_annmsg_id = [0]*len(extensions)
findmefollow_postdest = [str('ext-local,') + str(x) + str(',dest') for x in extensions]
findmefollow_dring = [None]*len(extensions)
findmefollow_needsconf = [None]*len(extensions)
findmefollow_remotealert_id = [0]*len(extensions)
findmefollow_toolate_id = [0]*len(extensions)
findmefollow_ringing = [None]*len(extensions)
findmefollow_pre_ring = [20]*len(extensions)          #Time in seconds for the call to ring at the first destination (extension) and then follow the direction specified by Follow Here. A value of 0 will skip the first step.
findmefollow_voicemail = ["novm"]*len(extensions)
findmefollow_calendar_id = [None]*len(extensions)
findmefollow_calendar_match = ["yes"]*len(extensions)
findmefollow_changecid = ["default"]*len(extensions)
findmefollow_fixedcid = [None]*len(extensions) 
findmefollow_enabled = ["Follow Me Enabled"]*len(extensions)
languages_language = [None]*len(extensions)
voicemail_enable = [None]*len(extensions)
voicemail_vmpwd = [None]*len(extensions)
voicemail_email = [None]*len(extensions)
voicemail_pager = [None]*len(extensions)
voicemail_options = [None]*len(extensions)
voicemail_same_exten = [None]*len(extensions)
disable_star_voicemail = [None]*len(extensions)
vmx_unavail_enabled = [None]*len(extensions)
vmx_busy_enabled = [None]*len(extensions)
vmx_temp_enabled = [None]*len(extensions)
vmx_play_instructions = [None]*len(extensions)
vmx_option_0_number = [None]*len(extensions)
vmx_option_1_number = [None]*len(extensions)
vmx_option_2_number = [None]*len(extensions)


panel1 =(99997, None, "panel", "novm", 60, None, None, None, None, None, None, None, None, None, None, "default", 99997, "sip", "SIP/99997", "fixed", 99997, "panel", None, None, "dontcare", "dontcare", "dontcare", "dontcare", "disabled", 10, "disabled", "enabled", 99997, 3, None, None, "no",  "panel <99997>", "no", "from-internal", None, None, None,  "rfc2833", "no", "no", "dynamic", "no", None, None, None, "yes", "0.0.0.0/0.0.0.0", 5060, "yes", 60, "no", "e5dc324e2dea34d79e6f0ce6c6789ac9", "pai", "accept", "chan_sip", "udp,tcp,tls", "yes", "friend", "yes", None, "ENABLED", "ringallv2-prim", 20, None, 99997, None, "ext-local,99997,dest", None, None, None, None, None, 0, "novm", None, "yes", "default", None, None, None, None, None, None, None, None, None, None, None, None, None, None, None, None, None)
panel2 =(99998, None, "panel", "novm", 60, None, None, None, None, None, None, None, None, None, None, "default", 99998, "sip", "SIP/99998", "fixed", 99998, "panel", None, None, "dontcare", "dontcare", "dontcare", "dontcare", "disabled", 10, "disabled", "enabled", 99998, 3, None, None, "no",  "panel <99998>", "no", "from-internal", None, None, None,  "rfc2833", "no", "no", "dynamic", "no", None, None, None, "yes", "0.0.0.0/0.0.0.0", 5060, "yes", 60, "no", "e5dc324e2dea34d79e6f0ce6c6789ac9", "pai", "accept", "chan_sip", "udp,tcp,tls", "yes", "friend", "yes", None, "ENABLED", "ringallv2-prim", 20, None, 99998, None, "ext-local,99998,dest", None, None, None, None, None, 0, "novm", None, "yes", "default", None, None, None, None, None, None, None, None, None, None, None, None, None, None, None, None, None)
panel3 =(99999, None, "panel", "novm", 60, None, None, None, None, None, None, None, None, None, None, "default", 99999, "sip", "SIP/99999", "fixed", 99999, "panel", None, None, "dontcare", "dontcare", "dontcare", "dontcare", "disabled", 10, "disabled", "enabled", 99999, 3, None, None, "no",  "panel <99999>", "no", "from-internal", None, None, None,  "rfc2833", "no", "no", "dynamic", "no", None, None, None, "yes", "0.0.0.0/0.0.0.0", 5060, "yes", 60, "no", "e5dc324e2dea34d79e6f0ce6c6789ac9", "pai", "accept", "chan_sip", "udp,tcp,tls", "yes", "friend", "yes", None, "ENABLED", "ringallv2-prim", 20, None, 99999, None, "ext-local,99999,dest", None, None, None, None, None, 0, "novm", None, "yes", "default", None, None, None, None, None, None, None, None, None, None, None, None, None, None, None, None, None)
                                    

rows = zip(extensions, password, name, voicemail, ringtimer, noanswer, recording, outboundcid, sipname, noanswer_cid, busy_cid, chanunavail_cid, noanswer_dest, busy_dest, chanunavail_dest, mohclass, id, tech, dial, devicetype, user, description, emergency_cid, hint_override, recording_in_external, recording_out_external, recording_in_internal, recording_out_internal, recording_ondemand, recording_priority, answermode, intercom, cid_masquerade, concurrency_limit, accountcode, allow, avpf, callerid, canreinvite, context, defaultuser, deny, disallow, dtmfmode, encryption, force_avp, host, icesupport, mailbox, namedcallgroup, namedpickupgroup, nat, permit, port, qualify, qualifyfreq, rtcp_mux, secret, sendrpid, sessiontimers, sipdriver, transport, trustrpid, type, videosupport, vmexten, callwaiting_enable, findmefollow_strategy, findmefollow_grptime, findmefollow_grppre, findmefollow_grplist, findmefollow_annmsg_id, findmefollow_postdest, findmefollow_dring, findmefollow_needsconf, findmefollow_remotealert_id, findmefollow_toolate_id, findmefollow_ringing, findmefollow_pre_ring, findmefollow_voicemail, findmefollow_calendar_id, findmefollow_calendar_match, findmefollow_changecid, findmefollow_fixedcid, findmefollow_enabled, languages_language, voicemail_enable, voicemail_vmpwd, voicemail_email, voicemail_pager, voicemail_options, voicemail_same_exten, disable_star_voicemail, vmx_unavail_enabled, vmx_busy_enabled, vmx_temp_enabled, vmx_play_instructions, vmx_option_0_number, vmx_option_1_number, vmx_option_2_number)
rows_pdf = list(zip(extensions_pdf, secret))

path = "/home/last/owncloud/Grandstream_panels/"+options.FILENAME
Path(path).mkdir(parents=True, exist_ok=True)

path_qr = "/home/last/owncloud/Grandstream_panels/"+options.FILENAME+"/qr/"
Path(path_qr).mkdir(parents=True, exist_ok=True)

with open(path+'/'+options.FILENAME+'.csv', 'w') as f:
    writer = csv.writer(f)
    writer.writerow(headers)
    for row in rows:
        writer.writerow(row)
    writer.writerow(panel1)
    writer.writerow(panel2)   
    writer.writerow(panel3)       
f.close()

#create pdf-table with extension-pass
pdf = FPDF()                                # New  pdf object
pdf.set_font("Arial", size=10)              # Font style
epw = pdf.w - 2*pdf.l_margin                # Witdh of document
col_width = pdf.w / 4.5                     # Column width in table
row_height = pdf.font_size * 1              # Row height in table
spacing = 1.3                               # Space in each cell
pdf.add_page()                              # add new page

pdf.cell(200, 10, txt=options.FILENAME+' '+options.server, ln=1, align="C")
for row in rows_pdf:
    for item in row:
        pdf.cell(col_width, row_height*spacing, txt=item, border=1)
    pdf.ln(row_height*spacing)

pdf.output(path+'/'+options.FILENAME+'.pdf')               # Create pdf file 
pdf.close()                                 # Close file


#generate qr-codes
for ext,passsw in zip(extensions,secret):
    string = '<?xml version="1.0" encoding="utf-8"?><AccountConfig version="1"><Account><RegisterServer>'+options.server+'</RegisterServer><UserID>'+str(ext)+'</UserID><AuthID>'+str(ext)+'</AuthID><AuthPass>'+str(passsw)+'</AuthPass><AccountName>'+str(ext)+'</AccountName><DisplayName>'+str(ext)+'</DisplayName></Account></AccountConfig>'
    print(string)
    qrcode = pyqrcode.create(string, error='L', version=27, mode='binary')
    qrcode.png(path_qr+'/'+'Account_'+str(ext)+'.png', scale=2, module_color=[0, 0, 0, 128], background=[255, 255, 255])
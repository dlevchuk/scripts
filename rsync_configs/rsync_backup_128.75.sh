#!/bin/bash
#yum install -y rsync
#/usr/bin/rsync --daemon --config=/etc/rsyncd.conf
#iptables -A INPUT -s  -p tcp --dport 8765 -j ACCEPT
date

syst_dir=/home/last/BACKUPs/backup_
srv_name=asterisk.odessa.tv
srv_ip=
srv_user=backup

srv_dir1=etc
srv_dir=db
srv_dir2=varlib
srv_dir3=varwww
echo "Start backup ${srv_name}"

mkdir -p ${syst_dir}${srv_name}/increment/
mkdir -p ${syst_dir}${srv_name}/current/

/usr/bin/rsync -avR --port=8765 --delete --password-file=/etc/rsyncd.scrt ${srv_user}@${srv_ip}::${srv_dir} ${syst_dir}${srv_name}/current/db/ --backup --backup-dir=${syst_dir}${srv_name}/increment/`date +%Y-%m-%d`/  
/usr/bin/rsync -avR --port=8765 --password-file=/etc/rsyncd.scrt ${srv_user}@${srv_ip}::${srv_dir1} ${syst_dir}${srv_name}/current/etc/ --backup --backup-dir=${syst_dir}${srv_name}/increment/`date +%Y-%m-%d`/  
#/usr/bin/rsync -avR --port=8765 --password-file=/etc/rsyncd.scrt ${srv_user}@${srv_ip}::${srv_dir2} ${syst_dir}${srv_name}/current/varlib/ --backup --backup-dir=${syst_dir}${srv_name}/increment/`date +%Y-%m-%d`/  
/usr/bin/rsync -avR --port=8765 --password-file=/etc/rsyncd.scrt ${srv_user}@${srv_ip}::${srv_dir3} ${syst_dir}${srv_name}/current/varwww/ --backup --backup-dir=${syst_dir}${srv_name}/increment/`date +%Y-%m-%d`/  

/usr/bin/find ${syst_dir}${srv_name}/increment/ -maxdepth 1 -type d -mtime +30 -exec rm -rf {} \;
date
date=`date +"%d-%m-%Y %H:%M"`
text="Finish backup ${srv_name} ${srv_ip} ${date}"
echo "Finish backup ${srv_name}"
curl -s --header 'Content-Type: application/json' --request 'POST' --data "{\"chat_id\":\"\",\"text\":\"$text\"}" "https://api.telegram.org//sendMessage"
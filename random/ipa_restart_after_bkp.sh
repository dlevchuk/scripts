#15 05 * * * root ipa_restart_after_bkp.sh > /dev/null 2>&1

#!/bin/bash

BACKUP_NAME="ipa-full.tar"
BACKUP_CHECK="$(ls /var/lib/ipa/backup/ipa-full-$(date +"%Y-%m-%d")-*/ipa-full.tar |grep -o ipa-full.tar)"
number_failed_services="$(/sbin/ipactl status | grep -c "STOPPED")"
directory_dervice="$(/sbin/ipactl status | grep -c "Directory Service: STOPPED")"

if [[ ""$BACKUP_CHECK"" = ""$BACKUP_NAME""  &&  ("$number_failed_services" -gt 1 || "$directory_dervice" -eq 1 )]]; then
     /sbin/ipactl restart --ignore-service-failure
else
     exit 0
fi

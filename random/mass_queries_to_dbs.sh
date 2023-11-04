#!/bin/bash

QUERY[1]=""
QUERY[2]=""
QUERY[3]=""
QUERY[4]=""
QUERY[5]=""
QUERY[6]=""
QUERY[7]=""


DB_NAMES="$(HOME=/etc/zabbix mysql -N -B -e 'show databases')";
SUF_FILENAME=queries;

for VAR_DB in $DB_NAMES; do
    for index in 1 2 3 4 5 6 7
    do
    VAR_DB_Q="$(echo $VAR_DB | sed "s/^/use /; s/$/; ${QUERY[index]}/")";
    VAR_RESULT="$(HOME=/etc/zabbix mysql -t -e "$VAR_DB_Q")" ;
    if ! [ -z "$VAR_RESULT" ]
    then
    echo -e "\n\nDB name is $VAR_DB - ${QUERY[index]}" >> $HOSTNAME'_'$SUF_FILENAME.txt;
    echo "$VAR_RESULT" >> $HOSTNAME'_'$SUF_FILENAME.txt;
    fi
    done
done 2> /dev/null;
#!/bin/bash

space=" "
del="\n"
ul="_"
logfile="/home/zabbix/telegram_log"
date=`date +"%d-%m-%y %H:%M"`
epoch=`date +%s`
let Vepoch=$epoch-300
base="/home/elc/sana/sqlite/sms/sms.sql"
date_base="/usr/lib/zabbix/alertscripts/duration.sql"
prebody=$1
body=`echo "$1" | sed "s/ /_/g"`
problem=`echo $body | grep PROBLEM | wc -m`
operstatus=`echo $body | grep "Operational_status" | wc -m`
noping=`echo $body | grep Unreachable | wc -m`
recovery=`echo $body | grep RECOVERY | wc -m`
no_process=`echo $body | grep running | wc -m`
Vjuniper=`echo $body | grep JunMX960 | wc -m`
trigger=`echo $body | sed 's;\/\/.*;;' | grep -o "Trigger:.*" | sed "s/Trigger\:\_//g"`
VCPU_util=`echo $body | grep CPU_Utilization | wc -m`
if [ $no_process -gt "0" ]; then
process=`echo $body | grep -o "No_process.*" | cut -d "_" -f3`
fi
##ip=`echo $body | sed 's;\/\/.*;;' | grep -o "_10.*" | sed 's;is.*;;' | sed 's/_//g'`

function get_data {
type=`echo "select type from switches where ip=\"$ip\" limit 1;" | sqlite3 $base`
devname=`echo "select name from switches where ip=\"$ip\" limit 1;" | sqlite3 $base`
street=`echo "select street_ru from switches where ip=\"$ip\" limit 1;" | sqlite3 $base  | tr " " "_"`
street_en=`echo "select street_ru from switches where ip=\"$ip\" limit 1;" | sqlite3 $base  | tr " " "_"`
brig_id=`echo "select brigada from switches where ip=\"$ip\";" | sqlite3 $base`
}


function debug {

echo "Vjuniper=$Vjuniper"
echo "Vjun_traffic=$Vjun_traffic"
echo "Vjun_status=$Vjun_status"


echo "##########################"  >> $logfile
echo "### BEGIN NEW MESSAGE (DEBUG MODE)"  >> $logfile
echo "Date= $date"  >> $logfile
echo "##########################"   >> $logfile
echo "Prebody= $prebody" >> $logfile
echo "$1" >> $logfile
echo "body=$body" >> $logfile
echo "trigger=$trigger" >> $logfile
echo "problem_status=$problem" >> $logfile
echo "Recovery_status=$recovery" >> $logfile
echo "noping_status=$noping" >> $logfile
echo "VCPU_util=$VCPU_util" >> $logfile
echo "ip=$ip" >> $logfile
echo "Type=$type" >> $logfile
echo "devname=$devname" >> $logfile
echo "street=$street" >> $logfile
echo "brig_id=$brig_id" >> $logfile
echo "Vjuniper=$Vjuniper" >> $logfile
echo "Vjun_traffic=$Vjun_traffic" >> $logfile
echo "Vjun_status=$Vjun_status" >> $logfile
echo "##########################"  >> $logfile
echo "state= $state"  >> $logfile
echo "descr= $descr"  >> $logfile
echo "operstatus=$operstatus"   >> $logfile
echo "interface_type=$interface_type"   >> $logfile
echo "interface_numb=$interface_numb"  >> $logfile
echo "##### END!!! !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"  >> $logfile
echo "" >> $logfile
echo "" >> $logfile
}

function noping {
        probdate=`date -d @$Vepoch | awk {'print $4'}`
	echo "epoch =$epoch; Vepoch = $Vepoch"
	echo "epoch =$epoch; Vepoch = $Vepoch" >> $logfile
	echo "date -d @$Vepoch | cut -d \"\" -f4"
	echo "probdate= $probdate"
	echo "probdate= $probdate" >> $logfile
        text="Status=Unavailable$del$devname$del$street$del$ip$del$probdate"
	echo "UPDATE switches SET status='Down' where ip='$ip';" | sqlite3 $date_base
	send_msg
}

function Recovery {
        echo "UPDATE switches SET status='Up' where ip='$ip';" | sqlite3 $date_base
        prob_time=`echo "select epoch from duration where ip='$ip' and state='P' order by id DESC limit 1;" | sqlite3 $date_base`
        rec_time=`echo "select epoch from duration where ip='$ip' and state='R' order by id DESC limit 1;" | sqlite3 $date_base`
        downtime=`python3 -c "a=($rec_time-$prob_time)/60;print(round(a,2))"`

	text="Status=Recovery$del$devname$del$street$del$ip \n Switch were unavailable$del$space$downtime minutes";
	echo $rec_time >> $logfile;
	echo $prob_time >> $logfile;
	echo $text >> $logfile;
	echo $downtime >> $logfile;
	send_msg
}

function jun_status_problem {
        text="Status=$state$del$descr"
        echo "text = $text" >> $logfile
        send_msg
}

function jun_status_recovery {
        text="Status=$state$del$descr"
        echo "text = $text" >> $logfile
        send_msg
}

function jun_traffic_problem {
        text="Status=$state$del$descr"
        echo "text = $text" >> $logfile
        send_msg
}

function jun_traffic_recovery {
        text="Status=$state$del$descr"
        echo "text = $text" >> $logfile
        send_msg
}


function CPU {
        text="Status=$state $del $descr"
        echo "text = $text" >>  $logfile
        send_msg
}

function CPUR {
        text="Status=$state $del $descr"
        echo "text = $text" >>  $logfile
        send_msg
}


function no_process {
        text="Status=$state $del $descr"     
	echo "text = $text" >>  $logfile
	send_msg
}

function no_process_recovery {
        text="Status=$state $del $descr"
	echo "text = $text" >>  $logfile
        send_msg
}

function agrigation_problem {
	text="Status=Unavailable! Agregation link$interface_type$ul$interface_numb on $link_to ($ip)."
	send_msg
        echo "UPDATE agrigation_int SET state='Down' where ip='$ip' and number='$interface_numb';" | sqlite3 $base
}
function agrigation_recovery {
        text="Status=Recovery! Agregation link$interface_type$ul$interface_numb on $link_to ($ip)."
	send_msg
        echo "UPDATE agrigation_int SET state='up' where ip='$ip' and number='$interface_numb';" | sqlite3 $base
}

function get_ip {
ip=`echo $body | sed 's;\/\/.*;;' | grep -o "_10.*" | sed 's;is.*;;' | sed 's/_//g'`
get_data
echo "#######@@@#####@@##@@##@@##"
echo "brig_id = $brig_id"
echo "ip=$ip"
echo "#######@@@#####@@##@@##@@##"
}

function get_CPU {
CPUutil=`echo $body | grep -o "values:.*"| cut -d"/" -f1 | cut -d":" -f3 | sed "s/_//"`
}

function sendunix {
curl -s --header 'Content-Type: application/json' --request 'POST' --data "{\"chat_id\":\"\",\"text\":\"$text\"}" "https://api.telegram.org/bo/sendMessage"
curl -s --header 'Content-Type: application/json' --request 'POST' --data "{\"chat_id\":\"\",\"text\":\"$text\"}" "https://api.telegram.org/bo/sendMessage"
curl -s --header 'Content-Type: application/json' --request 'POST' --data "{\"chat_id\":\"\",\"text\":\"$text\"}" "https://api.telegram.org/bot/sendMessage"
curl -s --header 'Content-Type: application/json' --request 'POST' --data "{\"chat_id\":\"\",\"text\":\"$text\"}" "https://api.telegram.org/bot/sendMessage"
}


function send_Krivoh {
curl -s --header 'Content-Type: application/json' --request 'POST' --data "{\"chat_id\":\"\",\"text\":\"$text\"}" "https://api.telegram.org/bot/sendMessage"
}

function sendall {
a=`curl -s --header 'Content-Type: application/json' --request 'POST' --data "{\"chat_id\":\"\",\"text\":\"$text\"}" "https://api.telegram.org/bot/sendMessage"`
echo "sendall = $a"
}

function send_msg {
if [ "$brig_id" = "1" ] || [ "$brig_id" = "2" ] || [ "$brig_id" = "3" ] || [ "$brig_id" = "5" ] || [ "$brig_id" = "57" ] || [ "$brig_id" = "78" ] || [ "$brig_id" = "142" ] || [ "$brig_id" = "175" ];
then ## DGS Group Malina
curl -s --header 'Content-Type: application/json' --request 'POST' --data "{\"chat_id\":\"-\",\"text\":\"$text\"}" "https://api.telegram.org/bot/sendMessage"

sendall
send_Krivoh
curl -s --header 'Content-Type: application/json' --request 'POST' --data "{\"chat_id\":\"\",\"text\":\"$text\"}" "https://api.telegram.org/bot/sendMessage"


elif [ "$brig_id" = "6" ] || [ "$brig_id" = "7" ] || [ "$brig_id" = "8" ] || [ "$brig_id" = "9" ]; 
then
curl -s --header 'Content-Type: application/json' --request 'POST' --data "{\"chat_id\":\"\",\"text\":\"$text\"}" "https://api.telegram.org/botc/sendMessage"
sendall
send_Krivoh

elif [ "$brig_id" = "172" ] || [ "$brig_id" = "190" ];
then 
curl -s --header 'Content-Type: application/json' --request 'POST' --data "{\"chat_id\":\"\",\"text\":\"$text\"}" "https://api.telegram.org/bot/sendMessage"
sendall
send_Krivoh

elif [ "$brig_id" = "41" ] || [ "$brig_id" = "42" ] || [ "$brig_id" = "4" ];
curl -s --header 'Content-Type: application/json' --request 'POST' --data "{\"chat_id\":\"\",\"text\":\"$text\"}" "https://api.telegram.org/bot/sendMessage"
sendall
send_Krivoh

elif [ "$brig_id" = "32" ] || [ "$brig_id" = "31" ]; 
then
curl -s --header 'Content-Type: application/json' --request 'POST' --data "{\"chat_id\":\"\",\"text\":\"$text\"}" "https://api.telegram.org/bot/sendMessage"
curl -s --header 'Content-Type: application/json' --request 'POST' --data "{\"chat_id\":\"\",\"text\":\"$text\"}" "https://api.telegram.org/bot/sendMessage"
sendall
send_Krivoh

elif [ "$brig_id" = "9999" ]; 
then
sendunix
sendall
else
sendall
fi
}

function logdb_recovery {
echo "insert into duration (ip, state, problem, epoch) values ('$ip', '$state', '$descr', '$epoch');" | sqlite3 $date_base
}

function logdb_problem {
echo "insert into duration (ip, state, problem, epoch) values ('$ip', '$state', '$descr', '$Vepoch');" | sqlite3 $date_base
echo "insert into duration (ip, state, problem, epoch) values ('$ip', '$state', '$descr', '$Vepoch');"
}

function main {
############################################################
if [ $problem -gt "0" ] && [ $noping -gt "0" ]; then
	state="P";
	descr="Unreachable"
	get_ip
	logdb_problem
	debug
	noping
elif [ $recovery -gt "0" ] && [ $noping -gt "0" ]; then           
	state="R";
	descr="Unreachable"
        get_ip
	logdb_recovery
        debug
	Recovery
elif [ $problem -gt "0" ] && [ $no_process -gt "0" ]; then
	state="PROBLEM";
        get_ip
	descr="No process $process running on $street_en $ip"
        debug
	no_process
elif [ $recovery -gt "0" ] && [ $no_process -gt "0" ]; then
	state="RECOVERY";
        get_ip
	descr="No process $process running on $street_en $ip"
        debug
	no_process_recovery
###################################################################### CPU Util
elif [ $problem -gt "0" ] && [ $VCPU_util -gt "0" ]; then
        state="PROBLEM";
        get_ip
	get_CPU
        descr="CPU util is $CPUutil on $ip"
        debug
        CPU
elif [ $recovery -gt "0" ] && [ $VCPU_util -gt "0" ]; then
        state="RECOVERY";
        get_ip
	get_CPU
        descr="CPU util is $CPUutil on $ip"
        debug
        CPUR
######################################################################### Juniper IF
elif [ $Vjuniper -gt "0" ]; then
	Vjun_traffic=`echo $trigger | grep "traffic" | wc -m`
	Vjun_status=`echo $trigger | grep "status_down_on" | wc -m`
	echo "trigger=$trigger"
	echo "problem=$problem"
	echo "recovery=$recovery"
	echo "Vjun_status=$Vjun_status"
	echo "Vjun_traffic=$Vjun_traffic"
	if [ $Vjun_status -gt "0" ] && [ $problem -gt "0" ]; then
		interface=`echo $body | sed 's;\/\/.*;;' | grep -o "Trigger:.*" | sed "s/Trigger\:\_//g" | cut -d"_" -f2`
		state="PROBLEM";
		get_ip
		descr="Interface $interface on Juniper is down!"
		debug
		jun_status_problem
	elif [ $Vjun_status -gt "0" ] && [ $recovery -gt "0" ]; then
		echo "im here status!"
		interface=`echo $body | sed 's;\/\/.*;;' | grep -o "Trigger:.*" | sed "s/Trigger\:\_//g" | cut -d"_" -f2`
                state="RECOVERY";
                get_ip
                descr="Interface $interface on Juniper is up!"
                debug
                jun_status_recovery
        elif [ $Vjun_traffic -gt "0" ] && [ $problem -gt "0" ]; then
                interface=`echo $body | sed 's;\/\/.*;;' | grep -o "Trigger:.*" | sed "s/Trigger\:\_//g" | cut -d"_" -f1`
                speed=`echo $body | grep -o "values:.*"| cut -d"/" -f1 | grep -o "Bytes.*" | grep -o ":.*" | sed 's;:;;'`
                state="PROBLEM";
                get_ip
                descr="Interface traffic  $interface on (10.0.0.1) is critical ($speed)!"
                debug
                jun_traffic_problem
        elif [ $Vjun_traffic -gt "0" ] && [ $recovery -gt "0" ]; then
                interface=`echo $body | sed 's;\/\/.*;;' | grep -o "Trigger:.*" | sed "s/Trigger\:\_//g" | cut -d"_" -f1`
                speed=`echo $body | grep -o "values:.*"| cut -d"/" -f1 | grep -o "Bytes.*" | grep -o ":.*" | sed 's;:;;'`
                state="RECOVERY";
                get_ip
                descr="Interface traffic  $interface on (10.0.0.1) mormalized ($speed)!"
                debug
                jun_traffic_recovery
	else
		text="С джунипером(10.0.0.1) something wrong!"
		curl -s --header 'Content-Type: application/json' --request 'POST' --data "{\"chat_id\":\"\",\"text\":\"$text\"}" "https://api.telegram.org//sendMessage"
		curl -s --header 'Content-Type: application/json' --request 'POST' --data "{\"chat_id\":\"\",\"text\":\"$text\"}" "https://api.telegram.org//sendMessage"
	fi

elif [ $problem -gt "0" ] && [ $operstatus -gt "0" ]; then
        state="PROBLEM";
        interface_type=`echo $body | cut -d"_" -f11`
        interface_numb=`echo $body | cut -d"_" -f12 | cut -d"/" -f2`
        echo "interface_type=$interface_type"
        echo "interface_numb=$interface_numb"
        if [ $interface_type = "TenGigabitEthernet" ] ;then
	echo "IM HERE"
            ip=`echo $body | sed 's;\/\/.*;;' | grep -o "_10.*" | sed 's/_//g' | sed 's;interface.*;;'`
            get data
            int_base=`echo "select * from agrigation_int where ip=\"$ip\" and number=\"$interface_numb\" limit 1;" | sqlite3 $base | wc -l`
	    echo "int base = $int_base"
                if [ $int_base -gt "0" ]; then
		    link_to=`echo "select link_2_ru from agrigation_int where ip=\"$ip\" and number=\"$interface_numb\" limit 1;" | sqlite3 $base`
                    echo "link_to=$link_to"
		    brig_id=9999
                    debug
                    agrigation_problem
		else
		    echo "IM ELSE1"
                     exit 0
                fi
        else
		echo "IM ELSE2"
            exit 0
        fi
elif [ $recovery -gt "0" ] && [ $operstatus -gt "0" ]; then
        state="RECOVERY";
        interface_type=`echo $body | cut -d"_" -f11`
        interface_numb=`echo $body | cut -d"_" -f12 | cut -d"/" -f2`
	echo "interface_type=$interface_type"
	echo "interface_numb=$interface_numb"
        if [ $interface_type = "TenGigabitEthernet" ] ;then
            ip=`echo $body | sed 's;\/\/.*;;' | grep -o "_10.*" | sed 's/_//g' | sed 's;interface.*;;'`
            int_base=`echo "select * from agrigation_int where ip=\"$ip\" and number=\"$interface_numb\" limit 1;" | sqlite3 $base | wc -l`
                if [ $int_base -gt "0" ]; then
                    link_to=`echo "select link_2_ru from agrigation_int where ip=\"$ip\" and number=\"$interface_numb\" limit 1;" | sqlite3 $base`
		    echo "link_to=$link_to"
                    brig_id=9999
                    debug
                    agrigation_recovery
                else
                     exit 0
                fi
        else
            exit 0
        fi
        
fi
}
echo "body=$body"
echo "problem_status=$problem"
echo "Recovery_status=$recovery"
echo "operstatus=$operstatus"
main
#!/bin/bash

ZABBIX_SERVER=''
HOST_IP=''
ZABBIX_USER=''
ZABBIX_PASS=''
API=""
HOST_STATUS="$1" #1-disable 0-enable


authenticate() {
         echo `curl -k -s -H 'Content-Type: application/json-rpc' -d "{\"jsonrpc\": \"2.0\",\"method\":\"user.login\",\"params\":{\"user\":\""api_user"\",\"password\":\""xVwv1uL5Q%5xpVXRx"\"},\"auth\": null,\"id\":0}" "https://zabbix.selectica.com/api_jsonrpc.php"`
     }

authToken=`echo  $(authenticate) | sed -n 's/.*result":"\(.*\)",.*/\1/p'`


get_hostid_byip() {
        echo `curl -k -s -H 'Content-Type: application/json-rpc' -d "{\"jsonrpc\":\"2.0\",\"method\":\"hostinterface.get\",\"params\":{\"output\":\"extend\",\"filter\":{\"ip\":\""$HOST_IP"\"}},\"auth\":\"$authToken\",\"id\":0}" $API`

        }

HOST_ID=`echo $(get_hostid_byip) | cut -d ':' -f 5 | cut -d ',' -f 1 | sed 's/\"//g'`


change_host_status() {
        echo `curl -k -s -H 'Content-Type: application/json-rpc' -d "{\"jsonrpc\":\"2.0\",\"method\":\"host.update\",\"params\":{\"hostid\":\"$HOST_ID\",\"status\":\""$HOST_STATUS"\"},\"auth\":\"$authToken\",\"id\":0}" $API`
}
echo $(change_host_status)


logout() {
echo `curl -k -s -H 'Content-Type: application/json-rpc' -d "{\"jsonrpc\":\"2.0\",\"method\":\"user.logout\",\"params\":[],\"auth\":\"$authToken\",\"id\":0}" $API`

}
echo $(logout)

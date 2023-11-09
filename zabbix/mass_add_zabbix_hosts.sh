
#!/bin/bash

ZABBIX_SERVER=''
ZABBIX_USER=''
ZABBIX_PASS=''
API=""
HOSTGROUPID=''
TEMPLATEID=''
IP='127.0.0.1'

HOSTNAMES=()


authenticate() {
         echo `curl -k -s -H 'Content-Type: application/json-rpc' -d "{\"jsonrpc\": \"2.0\",\"method\":\"user.login\",\"params\":{\"user\":\""$ZABBIX_USER"\",\"password\":\""$ZABBIX_PASS"\"},\"auth\": null,\"id\":0}" "https://zabbix.determine.com/api_jsonrpc.php"`
     }
AUTH_TOKEN=`echo  $(authenticate) | sed -n 's/.*result":"\(.*\)",.*/\1/p'`



for t in ${HOSTNAMES[@]}; do
# Create Host
create_host() {
        echo `curl -k -s -H 'Content-Type: application/json-rpc' -d "{\"jsonrpc\":\"2.0\",\"method\":\"host.create\",\"params\":{\"host\":\""$t"\",\"interfaces\":[{\"type\": 1,\"main\": 1,\"useip\": 1,\"ip\": \""$IP"\",\"dns\": \"\",\"port\": \"10050\"}],\"groups\": [{\"groupid\": \""$HOSTGROUPID"\"}],\"templates\": [{\"templateid\": \""$TEMPLATEID"\"}]},\"auth\":\""$AUTH_TOKEN"\",\"id\":0}" $API`
    }
output=$(create_host)
echo $output | grep -q "hostids"
rc=$?
if [ $rc -ne 0 ]
 then
     echo -e "Error in adding host ${t} at `date`:\n"
     echo $output | grep -Po '"message":.*?[^\\]",'
     echo $output | grep -Po '"data":.*?[^\\]"'
     echo $output
else
     echo -e "\nHost ${t} added successfully\n"
fi

done

logout() {
echo `curl -k -s -H 'Content-Type: application/json-rpc' -d "{\"jsonrpc\":\"2.0\",\"method\":\"user.logout\",\"params\":[],\"auth\":\"$authToken\",\"id\":0}" $API`

}
echo $(logout)

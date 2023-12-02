<?php
//selection/deletion of switches that are not in the database, not in zabbix and not pingedа

function ping($ip){

  $ping1 = "ping -c5 -i0.2 -w1 -q $ip";
  $ping2 = exec($ping1,$ping,$ping5);
  preg_match("/(\d+)(?=%)/",$ping[3],$packet_loss);
  $ping_stat = "0";
    if ($packet_loss[0]=="0"){
        $ping_stat = "ok";
     }
     else{
     	$ping_stat = "not ok";
     }
 return $ping_stat;
 }

function Curl($url,$header,$info){
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
    curl_setopt($ch,CURLOPT_POST, 1);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $info);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response);
    }

$zabbixUser = '';
$zabbixPass = '';
$zabbixUrl = '';
$header = array("Content-type: application/json-rpc");
$logininfo = '{"jsonrpc": "2.0","method":"user.login","params":{"user":"","password":""},"auth": null,"id":0}'; 
// Get token for autentification in Zabbix
$token_z = Curl($zabbixUrl,$header,$logininfo);
$token = $token_z->result;

function hostid_byip($ip) {
  global $token,$zabbixUrl,$header;

  $hostidinfo = array(
    'jsonrpc' => '2.0',
    'method' => 'host.get',
    "params" =>array(
         "output" => ["hostids"],
         "filter" => array(
             "ip" => "$ip",
            )
        ),
    "auth"=>$token,
    "id"=>1
  );

 $hostidi = json_encode($hostidinfo);
 $result_h = Curl($zabbixUrl,$header,$hostidi);
 $hostids = $result_h->result;
 $hostids = json_decode(json_encode($hostids),true);
 $hostidr = "";
 foreach ($hostids as $hostid ){
   $hostidr.=$hostid["hostid"];
 }
 return $hostidr;
 }

function del_host_byid($id) {
  global $token,$zabbixUrl,$header;

  $hostdel = array(
    'jsonrpc' => '2.0',
    'method' => 'host.delete',
    "params" =>array(
         "$id"
        ),
    "auth"=>$token,
    "id"=>1
  );

 $del = json_encode($hostdel);
 $result_h = Curl($zabbixUrl,$header,$del);
 
 }

class MyDB extends SQLite3
 {
    function __construct()
    {
        $this->open('');
    }
 }

$link2 = mysqli_connect("", "", "", "");

/* Проверка подключения */
if (mysqli_connect_errno()) {
    printf("Connection not established: %s\n", mysqli_connect_error());
    exit();
}

mysqli_query ($link2,"set names utf8");

$db = new MyDB();

$query = $db->query("SELECT ip,street 
	                 FROM switches WHERE ip 
	                 NOT LIKE'10.1.%'");

    while ($row1 = $query->fetchArray()) {
           
        $ip1 = $row1["ip"];
        $street = $row1["street"];
        
        $query_sw = "SELECT ip
                     FROM switchs
                     WHERE ip='$ip1'";
        $gg = ping($ip1);
        $gg2 = hostid_byip($ip1);
        $result_sw = mysqli_query($link2,$query_sw);

        if((mysqli_num_rows($result_sw) == '0') and ($gg=="not ok") and empty($gg2)){

            print ($ip1."  ".$street.PHP_EOL);
            $query1 = $db->query("DELETE 
	                 FROM switches WHERE ip='$ip1'
	                 LIMIT 1");
           
         }
         elseif((mysqli_num_rows($result_sw) == '0') and ($gg=="not ok") and ($gg2 > 0)){

            print ($ip1."  ".$street.PHP_EOL);
            del_host_byid($gg2);
         }

     }
$db->close();

?>
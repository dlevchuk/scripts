<?php 

include_once 'confs/config.php';

function get_in_translate_to_en($string, $gost=false){
	if($gost)
	{
		$replace = array("А"=>"A","а"=>"a","Б"=>"B","б"=>"b","В"=>"V","в"=>"v","Г"=>"G","г"=>"g","Д"=>"D","д"=>"d",
                "Е"=>"E","е"=>"e","Ё"=>"E","ё"=>"e","Ж"=>"Zh","ж"=>"zh","З"=>"Z","з"=>"z","И"=>"I","и"=>"i",
                "Й"=>"I","й"=>"i","К"=>"K","к"=>"k","Л"=>"L","л"=>"l","М"=>"M","м"=>"m","Н"=>"N","н"=>"n","О"=>"O","о"=>"o",
                "П"=>"P","п"=>"p","Р"=>"R","р"=>"r","С"=>"S","с"=>"s","Т"=>"T","т"=>"t","У"=>"U","у"=>"u","Ф"=>"F","ф"=>"f",
                "Х"=>"Kh","х"=>"kh","Ц"=>"Tc","ц"=>"tc","Ч"=>"Ch","ч"=>"ch","Ш"=>"Sh","ш"=>"sh","Щ"=>"Shch","щ"=>"shch",
                "Ы"=>"Y","ы"=>"y","Э"=>"E","э"=>"e","Ю"=>"Iu","ю"=>"iu","Я"=>"Ia","я"=>"ia","ъ"=>"","ь"=>"");
	}
	else
	{
		$arStrES = array("ае","уе","ое","ые","ие","эе","яе","юе","ёе","ее","ье","ъе","ый","ий");
		$arStrOS = array("аё","уё","оё","ыё","иё","эё","яё","юё","ёё","её","ьё","ъё","ый","ий");        
		$arStrRS = array("а$","у$","о$","ы$","и$","э$","я$","ю$","ё$","е$","ь$","ъ$","@","@");
                    
		$replace = array("А"=>"A","а"=>"a","Б"=>"B","б"=>"b","В"=>"V","в"=>"v","Г"=>"G","г"=>"g","Д"=>"D","д"=>"d",
                "Е"=>"Ye","е"=>"e","Ё"=>"Ye","ё"=>"e","Ж"=>"Zh","ж"=>"zh","З"=>"Z","з"=>"z","И"=>"I","и"=>"i",
                "Й"=>"Y","й"=>"y","К"=>"K","к"=>"k","Л"=>"L","л"=>"l","М"=>"M","м"=>"m","Н"=>"N","н"=>"n",
                "О"=>"O","о"=>"o","П"=>"P","п"=>"p","Р"=>"R","р"=>"r","С"=>"S","с"=>"s","Т"=>"T","т"=>"t",
                "У"=>"U","у"=>"u","Ф"=>"F","ф"=>"f","Х"=>"Kh","х"=>"kh","Ц"=>"Ts","ц"=>"ts","Ч"=>"Ch","ч"=>"ch",
                "Ш"=>"Sh","ш"=>"sh","Щ"=>"Shch","щ"=>"shch","Ъ"=>"","ъ"=>"","Ы"=>"Y","ы"=>"y","Ь"=>"","ь"=>"",
                "Э"=>"E","э"=>"e","Ю"=>"Yu","ю"=>"yu","Я"=>"Ya","я"=>"ya","@"=>"y","$"=>"ye");
                
		$string = str_replace($arStrES, $arStrRS, $string);
		$string = str_replace($arStrOS, $arStrRS, $string);
	}
        
	return iconv("UTF-8","UTF-8//IGNORE",strtr($string,$replace));
 }

function add_qoutes($n){
    return("'".$n."'");
 } 

function remoove_slash($n){
$gost = array("/"=>"");
return strtr($n, $gost);
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

function sendMessage($chat_id, $message) {
 file_get_contents($GLOBALS['api'] . '/sendMessage?chat_id=' . $chat_id . '&text=' . urlencode($message));
 }

function ping($ip){

  $ping1 = "ping -c5 -i0.2 -w1 -q $ip";
  $ping2 = exec($ping1,$ping,$ping5);
  preg_match("/(\d+)(?=%)/",$ping[3],$packet_loss);
  $ping_stat = "0";
    if ($packet_loss[0]=="0"){
        $ping_stat = "ok";
     }
 return $ping_stat;
 }

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
 foreach ($hostids as $hostid ){
     $hostidr = $hostid["hostid"]; 
     return $hostidr;
  }
 }

function host_create($name, $ip, $groupid, $templateid, $mac, $address){

 $type = "2"; // 1 -agent interface, 2 - snmp interface
 global $token,$zabbixUrl,$header;

 $new_host = array(
    "jsonrpc" => "2.0",
    "method" => "host.create",
    "params"=> array(
        "host"=> $name,
        "interfaces"=>[array(
                "type"=> $type,
                "main"=> 1,
                "useip"=> 1,
                "ip"=> $ip,
                "dns"=> "",
                "port"=> "161"
            )],
        "groups"=> [array(           
                "groupid"=> $groupid     
        )],
        "templates"=> [array(
                "templateid"=> $templateid
        )],
        "inventory_mode"=> 0,
        "inventory"=> array(
            "macaddress_a"=> $mac,
            "location"=> $address
        )
    ),
    "auth" =>  $token,
    "id" =>  1
 );

 $host_n = json_encode($new_host);
 $result_n = Curl($zabbixUrl,$header,$host_n);

 $pos = strpos(json_encode($result_n), 'error');

  if ($pos !== false) {
    return $message = json_encode($result);
  } else { 
    return $message = "Switch " .$name. " - " .$ip. " успешно добавлен в заббикс";
  }
 }


?>
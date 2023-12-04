#!/usr/bin/php -q
<?php

const DB_HOST_1 = "localhost";
const DB_LOGIN_1 = "phones";
const DB_PASS_1 = "";
const DB_NAME_1 = "phones";

$link1 =  mysqli_connect("10.0.0.38", "asterisk_73", "", "phones");

if (mysqli_connect_errno()) {
    printf(" Error %s\n", mysqli_connect_error());
    exit();
}

mysqli_query($link1, "set names utf8");


$message = "";
$temp = '';
$warning = '';
$error = '';
$alarm=0;
$date = date("H:i:s j-m-Y");
$date_sms = date("H:i:s");
$time_for_chk = date("G");
$day_for_chk = date("w");



$fd = fopen("php://stdin", "r");
while (!feof($fd)){
	$message .= fread($fd, 1024);
}


$Array_message=preg_split ("/[\s]+/",$message);
$str=trim($Array_message[2]);
$re = "/[\\d]+.[\\d]+.[\\d]+.[\\d]+/";  
preg_match($re, $str, $match);
$ip=trim($match[0]);
$uptime=trim($Array_message[4]);
$value=trim($Array_message[6]);
$mib=trim($Array_message[7]);
$int=trim($Array_message[8]);


$file = fopen("/home/scripts/pingers/pingers.log","a+");
fwrite($file, $message);
fclose($file);


function sendTelegram($ip, $group, $address, $temp, $error, $chk_time, $warning, $brigada_id=0){

	global $date;
	global $time_for_chk;
	global $day_for_chk;
	$time_ok = 1;
	$ch_door = 0;

if ($warning == 1 OR $warning == 11) {
	$ch_door = 1;
}


if($chk_time == 1 AND $ch_door == 1){

	if ($day_for_chk == 6 OR $day_for_chk == 0){
		$time_ok = 0;
	}
	else{
		if ($time_for_chk >= 9 AND $time_for_chk < 18){ 
			$time_ok = 0;
		}
		else{
			$time_ok = 1;	
		}

	}
}

if ($brigada_id != 0 AND $brigada_id) {//
	exec('curl --header \'Content-Type: application/json\' --request \'POST\' --data \'{"chat_id":"","text":"'.$address.'\n'.$error.'\n'.$date.'\nTemperature = '.$temp.' C"}\' "https://api.telegram.org/ /sendMessage"');
}

if ($group == 100) { 
	exec('curl --header \'Content-Type: application/json\' --request \'POST\' --data \'{"chat_id":"-","text":"'.$address.'\n'.$error.'\n'.$date.'\nTemperature = '.$temp.' C"}\' "https://api.telegram.org/ /sendMessage"');
	exec('curl --header \'Content-Type: application/json\' --request \'POST\' --data \'{"chat_id":"-","text":"'.$address.'\n'.$error.'\n'.$date.'\nTemperature = '.$temp.' C"}\' "https://api.telegram.org/ /sendMessage"');
}

if ($group == 1 AND $time_ok == 1) { 
	exec('curl --header \'Content-Type: application/json\' --request \'POST\' --data \'{"chat_id":"","text":"'.$address.'\n'.$error.'\n'.$date.'\nTemperature = '.$temp.' C"}\' "https://api.telegram.org/ /sendMessage"');
	exec('curl --header \'Content-Type: application/json\' --request \'POST\' --data \'{"chat_id":"-","text":"'.$address.'\n'.$error.'\n'.$date.'\nTemperature = '.$temp.' C"}\' "https://api.telegram.org/ /sendMessage"');
}
if ($group == 5) { 
	exec('curl --header \'Content-Type: application/json\' --request \'POST\' --data \'{"chat_id":"","text":"'.$address.'\n'.$error.'\n'.$date.'\nTemperature = '.$temp.' C"}\' "https://api.telegram.org/ /sendMessage"');
}

if ($group == 333) { 
	exec('curl --header \'Content-Type: application/json\' --request \'POST\' --data \'{"chat_id":"","text":"'.$address.'\n'.$error.'\n'.$date.'\nTemperature = '.$temp.' C"}\' "https://api.telegram.org/ /sendMessage"');
	//exit();
	return;
}

//Chat for all
exec('curl --header \'Content-Type: application/json\' --request \'POST\' --data \'{"chat_id":"-","text":"'.$address.'\n'.$error.'\n'.$date.'\nTemperature = '.$temp.' C"}\' "https://api.telegram.org/ /sendMessage"');



}

function sendSms($num, $address_pn, $error){

	global $date_sms;
	$address_pn = transliterate($address_pn);
	$error = transliterate($error);
	$text = $error.'_'.$address_pn.'_'.$date_sms;


	if ($ch = @curl_init()) 
	{ 
	  @curl_setopt($ch, CURLOPT_URL, "http:// /goip/en/dosend.php?USERNAME=&PASSWORD=&smsprovider=1&smsnum=$num&method=2&Memo=$text"); 
	  @curl_setopt($ch, CURLOPT_HEADER, false); 
	  @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	  @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); 
	  $data = @curl_exec($ch); 
	  @curl_close($ch); 
	} 



	$pieces = explode("=", $data);
	$pieces1 = explode("&", $pieces['9']);
	$cnt = $pieces1['0'];

	if ($ch1 = @curl_init()) 
	{ 
	  @curl_setopt($ch1, CURLOPT_URL, "http:// /goip/en/resend.php?messageid=$cnt&USERNAME=&PASSWORD="); 
	  @curl_setopt($ch1, CURLOPT_HEADER, false); 
	  @curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true); 
	  @curl_setopt($ch1, CURLOPT_CONNECTTIMEOUT, 30); 
	  $data1 = @curl_exec($ch1); 
	  @curl_close($ch1); 
	}

}

function transliterate($input){
	$gost = array(
	   "Є"=>"YE","І"=>"I","Ѓ"=>"G","і"=>"i","№"=>"#","є"=>"ye","ѓ"=>"g",
	   "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D",
	   "Е"=>"E","Ё"=>"YO","Ж"=>"ZH",
	   "З"=>"Z","И"=>"I","Й"=>"J","К"=>"K","Л"=>"L",
	   "М"=>"M","Н"=>"N","О"=>"O","П"=>"P","Р"=>"R",
	   "С"=>"S","Т"=>"T","У"=>"U","Ф"=>"F","Х"=>"H",
	   "Ц"=>"C","Ч"=>"Ch","Ш"=>"Sh","Щ"=>"Sch","Ъ"=>"'",
	   "Ы"=>"Y","Ь"=>"","Э"=>"E","Ю"=>"Yu","Я"=>"Ya",
	   "а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d",
	   "е"=>"e","ё"=>"yo","ж"=>"zh",
	   "з"=>"z","и"=>"i","й"=>"j","к"=>"k","л"=>"l",
	   "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
	   "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
	   "ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"",
	   "ы"=>"y","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
	   " "=>"_","—"=>"_",","=>"_","!"=>"_","@"=>"_",
	   "#"=>"No","$"=>"","%"=>"","^"=>"","&"=>"","*"=>"",
	   "("=>"",")"=>"","+"=>"","="=>"",";"=>"",":"=>"",
	   "\'"=>"","\""=>"\"","~"=>"","`"=>"","?"=>"","/"=>"/",
	   "\\"=>"\\","["=>"","]"=>"","{"=>"","}"=>"","|"=>""
	  );

	return strtr($input, $gost);
}

function checkSt($ip, $sensor, $int, $warning){


	global $link1;

	$query = mysqli_query($link1,"SELECT $sensor FROM secur WHERE ip='$ip'");
	$row = mysqli_fetch_assoc($query);

	if ($row["$sensor"] == $int) {
		exit();
	}
	else{
		mysqli_query($link1,"UPDATE secur SET $sensor ='$int', warning = $warning WHERE ip='$ip'");	
	}
}

$temp=exec("/usr/bin/snmpwalk -v2c -c public -OUqv $ip iso.3.6.1.4.1.35160.1.16.1.13.2")/10;
$temp = (int)$temp;

$query_pn = "SELECT brigada_id, group_id, address, phones, no_signal, test, chk_time
                FROM secur s
                JOIN brigade_phones b ON s.brigada_id = b.id 
                WHERE s.ip = '$ip'";

$result1 = mysqli_query($link1,$query_pn);            
$result_pn = mysqli_fetch_assoc($result1);
if(mysqli_num_rows($result1) > 0){

    $brigada_id_pn = $result_pn['brigada_id'];
    $group_id_pn = $result_pn['group_id'];
    $address_pn = $result_pn['address'];
    $phones_pn = $result_pn['phones'];
    $signal_pn = $result_pn['no_signal'];
    $test_pn = $result_pn['test'];
    $chk_time = $result_pn['chk_time'];
}



/*
if(($mib=="iso.3.6.1.4.1.35160.1.26")){ 
	$door_st=exec("/usr/bin/snmpwalk -v2c -c public -OUqv $ip iso.3.6.1.4.1.35160.1.15.1.7.1");
	if ($door_st == 1) {
		$error="Door open";
		$warning=1;
		$sms=1;
		checkSt($ip, 'door', 1, $warning);
		exec('curl --header \'Content-Type: application/json\' --request \'POST\' --data \'{"chat_id":"","text":"'.$address_pn.'\n'.$error.'\n'.$date.'\nTemperature = '.$temp.' C"}\' "https://api.telegram.org/ /sendMessage"');
		// exit();	
	}
	elseif($door_st == 0){
		$error="Door closed";
		$warning=11;
		$sms=1;
		checkSt($ip, 'door', 2, $warning);
		exec('curl --header \'Content-Type: application/json\' --request \'POST\' --data \'{"chat_id":"","text":"'.$address_pn.'\n'.$error.'\n'.$date.'\nTemperature = '.$temp.' C"}\' "https://api.telegram.org/ /sendMessage"');
		// exit();		

		exec("/usr/bin/snmpset -v2c -c privates -OUqv $ip .1.3.6.1.4.1.35160.1.11.1.4.1 i 1");	

	}

}
*/


if(($mib=="iso.3.6.1.4.1.35160.1.26")&&($value=="iso.3.6.1.4.1.35160.1.0.12")){ 
	$error="Power on";
	$error_en="Power on";
	$warning=44;
	checkSt($ip, 'power', 1, $warning);
}
if(($mib=="iso.3.6.1.4.1.35160.1.26")&&($value=="iso.3.6.1.4.1.35160.1.0.11")){
	$error=" Power off";
	$error_en=" Power off";
	$warning=4;
	checkSt($ip, 'power', 2, $warning);
}




if(($mib=="iso.3.6.1.4.1.35160.1.29")&&($value=="iso.3.6.1.4.1.35160.1.0.3")&&($int==4)){
	$error="Door closed";
	$warning=11;
	$sms=1;
	checkSt($ip, 'door', 2, $warning);
}
if(($mib=="iso.3.6.1.4.1.35160.1.29")&&($value=="iso.3.6.1.4.1.35160.1.0.4")&&($int==4)){
	$error="Door opened";
	$warning=1;
	$sms=1;
	checkSt($ip, 'door', 1, $warning);
}



if(($mib=="iso.3.6.1.4.1.35160.1.29")&&($value=="iso.3.6.1.4.1.35160.1.0.3")&&($int==1)){
	$error="Door opened";
	$error_en="Door opened";
	$warning=11;
	$alarm=1;
	$sms=1;
	checkSt($ip, 'door', 1, $warning);
}
if(($mib=="iso.3.6.1.4.1.35160.1.29")&&($value=="iso.3.6.1.4.1.35160.1.0.4")&&($int==1)){
	$error="Door closed";
	$error_en="Door closed";
	$warning=1;
	$alarm=2;
	$sms=1;
	checkSt($ip, 'door', 2, $warning);
}



if(($mib=="iso.3.6.1.4.1.35160.1.22")&&($value=="iso.3.6.1.4.1.35160.1.0.9")){
	$error="Knock sensor alarm start";
	$warning=3;
	checkSt($ip, 'knock', 1, $warning);	
}
if(($mib=="iso.3.6.1.4.1.35160.1.22")&&($value=="iso.3.6.1.4.1.35160.1.0.10")){
	$error="Knock sensor alarm end";
	$warning=33;
	checkSt($ip, 'knock', 2, $warning);
}



if(($mib=="iso.3.6.1.4.1.35160.1.30")&&($value=="iso.3.6.1.4.1.35160.1.0.7")&&($int==2)){
	$error="Temperature high";
	$warning=2;
	$temp=exec("/usr/bin/snmpwalk -v2c -c public -OUqv $ip iso.3.6.1.4.1.35160.1.16.1.13.2")/10;
	$temp = (int)$temp;
	if ($temp < 30) {
		exit();
	}
	checkSt($ip, 'temp', 1, $warning);
}
if(($mib=="iso.3.6.1.4.1.35160.1.30")&&($value=="iso.3.6.1.4.1.35160.1.0.5")&&($int==2)){
	$error="Temperature low";
	$warning=22;
	checkSt($ip, 'temp', 2, $warning);
}
if(($mib=="iso.3.6.1.4.1.35160.1.30")&&($value=="iso.3.6.1.4.1.35160.1.0.6")&&($int==2)){
	$error="Temperature very high";
	$warning=222;
	checkSt($ip, 'temp', 0, $warning);
}
if(($mib=="iso.3.6.1.4.1.35160.1.30")&&($value=="iso.3.6.1.4.1.35160.1.0.8")&&($int==2)){
	$error="Temperature very low";
	$warning=2222;
	checkSt($ip, 'temp', 0, $warning);
}



if(($mib=="iso.3.6.1.4.1.35160.1.28")&&($value=="iso.3.6.1.4.1.35160.1.0.1")){
	$error="Alarm start";
	$warning=88;
	$alarm=1;
	checkSt($ip, 'alarm', 1, $warning);
}
if(($mib=="iso.3.6.1.4.1.35160.1.28")&&($value=="iso.3.6.1.4.1.35160.1.0.2")){
	$error="Alarm end";
	$warning=8;
	checkSt($ip, 'alarm', 2, $warning);
}


// exec("/usr/bin/snmpset -v2c -c privates -OUqv $ip .1.3.6.1.4.1.35160.1.11.1.4.1 i 2"); // Disable alarm
// exec("/usr/bin/snmpset -v2c -c privates -OUqv $ip .1.3.6.1.4.1.35160.1.11.1.4.1 i 1"); // Enable alarm


if ($alarm == 1) {
	// sleep(30);
	// exec("/usr/bin/snmpset -v2c -c privates -OUqv $ip .1.3.6.1.4.1.35160.1.11.1.4.1 i 2");
}
if ($alarm == 2) {
	//exec("/usr/bin/snmpset -v2c -c privates -OUqv $ip .1.3.6.1.4.1.35160.1.11.1.4.1 i 1");	
}

$query1 = "INSERT INTO `secur_log` 
	(`ip`,`temp`, `uptime`, `mib`, `value`, `time`, `message`, `error`, `warning`) 
	VALUES ('$ip', '$temp', '$uptime', '$mib', '$value', NOW(), '$message', '$error', '$warning')"; 
mysqli_query($link1,$query1);

if ($test_pn == 1) {
	if ($signal_pn == 0) {		
			if ($alarm == 1) {
				// sleep(30);
				exec("/usr/bin/snmpset -v2c -c privates -OUqv $ip .1.3.6.1.4.1.35160.1.11.1.4.1 i 1");
			}
			if ($alarm == 2) {
				exec("/usr/bin/snmpset -v2c -c privates -OUqv $ip .1.3.6.1.4.1.35160.1.11.1.4.1 i 2");	
			}
	}
	sendTelegram($ip, 333, $address_pn, $temp, $error, $chk_time, $warning);	
	exit();

}


if ($warning > 0) {
	
	// if ($signal_pn == 0) {
	// 	exec("/usr/bin/snmpset -v2c -c privates -OUqv $ip .1.3.6.1.4.1.35160.1.11.1.4.1 i 2");
	// }
	// elseif($signal_pn == 1){
	// 	exec("/usr/bin/snmpset -v2c -c privates -OUqv $ip .1.3.6.1.4.1.35160.1.11.1.4.1 i 2");
	// }
	exec("/usr/bin/snmpset -v2c -c privates -OUqv $ip .1.3.6.1.4.1.35160.1.11.1.4.1 i 2");
	sendTelegram($ip, $group_id_pn, $address_pn, $temp, $error, $chk_time,  $warning, $brigada_id_pn);
	if ($sms == 1 && $phones_pn > 0) {
		
		if ($brigada_id_pn != 5) {
			sendSms($phones_pn, $address_pn, $error);
		}
		
	}
	exit();



}

	
exit();
?>
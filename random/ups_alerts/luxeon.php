#!/usr/bin/php
<?php
//*/2 * * * *   /usr/bin/php /home/last/Scripts/ups_alerts/luxeon.php

$chat_id = "";

$access_token = '';
$api = 'https://api.telegram.org/bot' . $access_token;

function sendMessage($chat_id, $message) {
     file_get_contents($GLOBALS['api'] . '/sendMessage?chat_id=' . $chat_id . '&text=' . urlencode($message));
}

exec ("wget --quiet -O /home/last/Scripts/ups_alerts/upslog.txt http://10.0.0.36:8182/Rupsmon.evt.txt",$kk1,$ggg);
//exec ("wget --quiet -O /home/last/Scripts/ups_alerts/upslog.txt https://lasttest.odessa.tv/1.php");

if ($ggg != "0"){
	sendMessage($chat_id,"Can't connect 10.0.0.36 to take info about luxeon ups");
 }

$file = "/home/last/Scripts/ups_alerts/upslog_old.txt";
$file2 = "/home/last/Scripts/ups_alerts/upslog.txt";

$aa = "sort $file";
$bb = "sort $file2";

$cc = exec ($aa,$aa1);
$dd = exec ($bb,$bb1);

$gg = array_diff($bb1,$aa1);
if (empty($gg)){
	exit(0);
}
//var_dump($gg);
else {
	foreach( $gg as $zz) {
 	//       var_dump($zz);
 		$zz = 'LUXEON'.PHP_EOL.$zz; 
		sendMessage($chat_id,$zz);
	}
   
	$mm = "mv -f $file2 $file";
	$m1 = exec($mm,$m2);
}
?>
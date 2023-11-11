<?php
//script for parse switchlog and sent it to telegram 
//download log from 10.0.0.95, compare it with log downloaded last time, parse, sent to telegram and rewrite switch_old.log 

$chat_id = "";
$access_token = '';
$api = 'https://api.telegram.org/bot' . $access_token;

function sendMessage($chat_id, $message) {
     file_get_contents($GLOBALS['api'] . '/sendMessage?chat_id=' . $chat_id . '&text=' . urlencode($message));
}

$connection = ssh2_connect('10.0.0.95', 22);
ssh2_auth_password($connection, '', '');
if (!$connection) die('Connection failed');
ssh2_scp_recv($connection, '/var/log/switch', '/home/last/Scripts/switchlog/switch.log');


$file2 = "/home/last/Scripts/switchlog/switch.log";
$file = "/home/last/Scripts/switchlog/switch_old.log";

if (file_exists($file) && is_readable ($file) && file_exists($file2) && is_readable ($file2)) {
   exec ("comm -23 --nocheck-order $file2 $file > /home/last/Scripts/switchlog/switch1.log",$ll);
 }

$filename = "/home/last/Scripts/switchlog/switch1.log";
if (file_exists($filename) && is_readable ($filename)) {
  
  $aa0 = "cat $filename | grep DHCP";  
  $aa1 = "cat $filename | grep -E 'Management.*ACL'";  
  $aa2 = "cat $filename | grep -E 'Login.*fail'";  
  $aa3 = "cat $filename | grep LBD";  
  $aa4 = "cat $filename | grep -E 'Multicast.*storm'";  
  $aa5 = "cat $filename | grep -E 'Broadcast.*storm'";  
  $aa6 = "cat $filename | grep -E 'Internal.*Power'";  
  $aa7 = "cat $filename | grep Fan";  
  //$aa7 = "cat $filename | grep Spanning";  
  //$aa8 = "cat $filename | grep -E 'Safeguard.*Engine'";  
  //$aa9 = "cat $filename | grep -E 'Possible.*spoofing'";  
  //$aa10 = "cat $filename | grep -E 'Land.* Attack'";
  //$aa11 = "cat $filename | grep -E 'Ping.*of.*Death.*Attack'";

  $cc0 = exec ($aa0,$aaa) ;
  $cc1 = exec ($aa1,$aaa) ;
  $cc2 = exec ($aa2,$aaa) ;
  $cc3 = exec ($aa3,$aaa) ;
  $cc4 = exec ($aa4,$aaa) ;
  $cc5 = exec ($aa5,$aaa) ;
  $cc6 = exec ($aa6,$aaa) ;
  $cc7 = exec ($aa7,$aaa) ;
  //$cc8 = exec ($aa8,$aaa) ;
  //$cc9 = exec ($aa9,$aaa) ;
  //$cc10 = exec ($aa10,$aaa) ;
  //$cc11 = exec ($aa11,$aaa) ;

  $result=array();
  $k='';
  //var_dump($aaa);
  foreach( $aaa as $zz) {
   $zz = preg_replace('/.*([0-9]{0,2}:[0-9]{0,2}:[0-9]{0,2})\s/','',$zz);
   $result[$k] = $zz ;
   $k++;
    }

  if (!empty($result)){   
     $result1 = (array_count_values($result));
    }
  else {
   exit();
  }

  foreach ($result1 as $key => $con) {
    $ket= $key. '=>' .$con.PHP_EOL;
//   sendMessage($chat_id,$ket);
   }

 /*way to delete repeats without count
   foreach( $aaa as $zz) {
   $zz = preg_replace('/.*([0-9]{0,2}:[0-9]{0,2}:[0-9]{0,2})\s/','',$zz);
   $result[$zz] = null;  
   }
   if (!empty($result)){   
   $value = array_keys($result);
   }
   else {
   exit();
   }
   foreach ($value as $key ) {
   sendMessage($chat_id,$key);
   }
 */

}

$mm = "cat $filename > $file";
$m1 = exec($mm,$m2);

?>
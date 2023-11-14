<?php

error_reporting(E_ALL); 
ini_set("display_errors", 0); 

include_once 'confs/functions.php';
include_once 'confs/config.php';


$query = "SELECT zab_group_id FROM `switch_type` WHERE `zab_group_id` > 0";
$result1 = mysqli_query($link_local,$query);

$count_id_zab = 0;
$id[] = '';
if(mysqli_num_rows($result1) > 0){
     while ($row = mysqli_fetch_array($result1)){
         $id[] = $row['zab_group_id'];
         $count_id_zab++;
        }
  } else {
       $message =  "Error message = ".mysqli_error($link_local);
       sendMessage($GLOBALS['chat_id'],$message);
  } 

unset($id[0]);

function get_zab_hostip($hostid_array) {
     
     $ip_arr = array();
     $count_ip_arr = 0; 
     $get_hostibterface = array(
         "jsonrpc" =>  "2.0",
         "method" =>  "hostinterface.get",
         "params" =>  array(
             "output" =>  "extend",
             "hostids" => $hostid_array
         ),
         "auth" =>  $GLOBALS['token'],
         "id" =>  1
     );
 
     $result = Curl($GLOBALS['zabbixUrl'],$GLOBALS['header'],json_encode($get_hostibterface))->result;
     $res = json_decode(json_encode($result),true);
     
     foreach ($res as $ip ){
         $ip_arr[] = $ip["ip"];
         $count_ip_arr++;
         }
     
     $zab_ip = []; 
     $zab_ip = array_unique($ip_arr);

     $query = "SELECT ip FROM `switches` WHERE `type` = 'switch'";
     $result = mysqli_query($GLOBALS['link_local'],$query);

     $count_ip_base = 0;
     $ip_b[] = '';
     
     if(mysqli_num_rows($result) > 0){
         while ($row = mysqli_fetch_array($result)){
             $ip_b[] = $row['ip'];
             $count_ip_base++;
             }
     } else {
         $message =  "Error message = ".mysqli_error($link_local);
         sendMessage($GLOBALS['chat_id'],$message);
     }

     $base_ip = array_unique($ip_b);
     $dd = array_diff($zab_ip, $base_ip);
     $ff = array_diff($base_ip, $zab_ip);
   
     foreach ($ff as $ip){
         $ip_elan = '';
         preg_match('/(10)\.(1)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)/',$ip,$ip_elan);
    
         if (empty($ip_elan[0]) and !empty($ip)) {
             $message = "IP который есть в базе 10.0.0.57 но нет в заббиксе - ".$ip;
             sendMessage($GLOBALS['chat_id'],$message);
             } 
         } 

     foreach ($dd as $ip){
         $ip_elan = '';
         preg_match('/(10)\.(1)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)/',$ip,$ip_elan);
    
         if (empty($ip_elan[0]) and !empty($ip)) {
             $message = "IP который есть в заббиксе но нет в базе 10.0.0.57 - ".$ip;
             sendMessage($GLOBALS['chat_id'],$message);
             } 
         }



 }

function get_zabhost_by_groupid($array_groupids){
 $ip_arr = [];
 $count_ip_arr = 0; 
    $get_hosts = array(
        "jsonrpc" =>  "2.0",
        "method" =>  "host.get",
        "params" =>  array(
            "output" =>  "hostid",
            "groupids" => $array_groupids
        ),     
        "auth" => $GLOBALS['token'],
        "id" =>  1
    );

 $result = Curl($GLOBALS['zabbixUrl'],$GLOBALS['header'],json_encode($get_hosts));
 $result = $result->result;
 $result = json_decode(json_encode($result),true);
 foreach ($result as $hostid ){
     $ip_arr[] = $hostid["hostid"]; 
     $count_ip_arr++;
     }
 get_zab_hostip($ip_arr);
 }
 
get_zabhost_by_groupid($id);


?>
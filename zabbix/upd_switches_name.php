<?php
error_reporting(E_ALL);
ini_set("display_errors", 0);

include_once 'confs/functions.php';
include_once 'confs/config.php';

$query_sw = "SELECT ip,address,type
             FROM switchs
             WHERE ip like '10.1.%' OR ip like '10.252.%'";


$result_sw = mysqli_query($link_nika,$query_sw);
if(mysqli_num_rows($result_sw) > 0){
   while ($row = mysqli_fetch_assoc($result_sw)){
     $ip_sw = $row['ip'];
     $address = $row['address'];
     $type = $row['type'];
     $query_type = "SELECT name
                    FROM switch_type
                    WHERE id = '$type'";
     $result_type = mysqli_query($link_nika,$query_type);
     while ($row3 = mysqli_fetch_assoc($result_type)){
        $type_name = $row3['name'];
      }
     $address_en = get_in_translate_to_en($address);
     $type_s = 'switch';
     $brigada = '207';
     $query1 = "INSERT INTO switches (name, ip, type, brigada, street, street_ru)
                SELECT * FROM (SELECT '$type_name', '$ip_sw', '$type_s', '$brigada', '$address_en', '$address') AS tmp
                WHERE NOT EXISTS (
                SELECT name FROM switches WHERE ip = '$ip_sw') LIMIT 1;";
     mysqli_query($link_local,$query1);
     $query2 = "UPDATE switches SET name='$type_name',type='switch', brigada='207', street='$address_en', street_ru='$address       ' WHERE ip = '$ip_sw';";
     mysqli_query($link_local,$query2);
  }
 }

$ip[] = '';
$count_ip_zab = 0;
$query_sw_sana = "SELECT ip FROM switches
                  WHERE ip not like'10.1.%' OR ip not like '10.252.%' AND type='switch'";
$result_sw1 = mysqli_query($link_local,$query_sw_sana);
if(mysqli_num_rows($result_sw1) > 0){
   while ($row = mysqli_fetch_assoc($result_sw1)){
      $ip[] = $row['ip'];
      $count_ip_zab++;
   }
}

unset($ip[0]);
$ip_n = array_map("add_qoutes", $ip);

$query_new_names = "SELECT address,ip FROM switchs
                    WHERE ip IN (".implode(",",$ip_n).")";
$result_names = mysqli_query($link_nika,$query_new_names);
if(mysqli_num_rows($result_names) > 0) {
   while ($row = mysqli_fetch_assoc($result_names)){
      $addres = $row['address'];
      $addres_en = get_in_translate_to_en($addres);
      $ip_sw1 = $row['ip'];
      $query_upd = "UPDATE switches SET street='$addres_en', street_ru='$addres' WHERE ip = '$ip_sw1';";
      mysqli_query($link_local,$query_upd);
   }
}

?>
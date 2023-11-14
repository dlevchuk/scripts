<?php
error_reporting(E_ALL); 
ini_set("display_errors", 0); 

include_once 'confs/functions.php';
include_once 'confs/config.php';
//switch type dgs from nika
//  '6', '5', '7', '11', '22', '28', '30', '31', '32', '34','36', '37', '39', '40', '41', '43', '45', '52', '53', '54', '56', '57', '58', '59', '67')

$query_ns = "SELECT COUNT(*) FROM switch_type";
$result_ns = mysqli_query($link_local,$query_ns);
$result_ns1 = mysqli_query($link_nika,$query_ns);
if ((mysqli_num_rows($result_ns) > 0) and (mysqli_num_rows($result_ns1) > 0)){
     $row = mysqli_fetch_array($result_ns);
     $row1 = mysqli_fetch_array($result_ns1);
     $num_rows = $row[0];
     $num_rows1 = $row1[0];
     if ($num_rows !== $num_rows1){
         $query_nt = "SELECT name FROM switch_type WHERE 1";
         $result_nt = mysqli_query($link_nika,$query_nt);
         $result_nt1 = mysqli_query($link_local,$query_nt);
         if ((mysqli_num_rows($result_nt) > 0) and (mysqli_num_rows($result_nt1) > 0)){
             while ($res = mysqli_fetch_assoc($result_nt)) {
                 $ar1[] = $res['name'];
                 }
             while ($res1 = mysqli_fetch_assoc($result_nt1)) {
                 $ar2[] = $res1['name'];
                 }
             foreach (array_diff($ar1, $ar2) as $nst) {
                 $nst = add_qoutes($nst);
                 $query_add_nst = "INSERT INTO switch_type (`name`) VALUES ($nst)";
                 $res_nst= mysqli_query($link_local,$query_add_nst);
                 if ($result_nst) {
                     sendMessage($chat_id,"В базе(10.0.0.38) появилась новая модель свича $nst (проверить нужно ли ее мониторить?(создавать темлейт и группу в заббиксе и добавить переменную группы в графану))");
                 } else {
                     $message =  "Error message = ".mysqli_error($link_local);
                     sendMessage($chat_id,$message);
                 }
                 }       
         } else {
            $message =  "Error message = " . mysqli_error($link_local) . mysqli_error($link_nika);
            sendMessage($chat_id,$message);
         }
     } else {
             $message = "В базе 10.0.0.38 не появилось новых моделей свитчей";
             sendMessage($chat_id,$message);
     }
 } else {
     $message =  "Error message = " . mysqli_error($link_local) . mysqli_error($link_nika);
     sendMessage($chat_id,$message);
 }


$query = "SELECT ip FROM switches WHERE ip NOT LIKE'10.1.%'
          AND type = 'switch'";
$result1 = mysqli_query($link_local,$query);

$count_ip_zab = 0;
$ip[] = '';
if(mysqli_num_rows($result1) > 0){
     while ($row = mysqli_fetch_array($result1)){
         $ip[] = $row['ip'];
         $count_ip_zab++;
        }
  } else {
       $message =  "Error message = ".mysqli_error($link_local);
       sendMessage($chat_id,$message);
  }                          

unset($ip[0]);
$ip_n = array_map("add_qoutes", $ip);

$query2 = "SELECT id,ip,address,type,mac FROM switchs 
WHERE type IN ('6', '5', '7', '11', '22', '28', '30', '31', '32', '34','36', '37', '39', '40', '41', '43', '45', '52', '53', '54', '56', '57', '58', '59', '67')
AND ip NOT IN (".implode(",",$ip_n).")
AND ip NOT LIKE '10.211.1.136'
AND ip NOT LIKE '10.214.1.36'
AND ip NOT LIKE '10.212.2.235'
AND ip NOT LIKE '10.212.9.9'
AND address NOT LIKE '%new%'
AND address NOT LIKE '%ремон%'
AND address NOT LIKE '%Снят для испол%'
AND ip REGEXP '(10)\\.(2[0-1][0-9])\\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)'
"; 

$result2 = mysqli_query($link_nika,$query2);


if(mysqli_num_rows($result2) > 0){
    $count_ip = 0;
    while ($row = mysqli_fetch_assoc($result2)){
         $ip_switch = $row['ip'];
         $id_switch = $row['id'];
         $address_switch_ru = $row['address'];
         $address_switch_en = get_in_translate_to_en($address_switch_ru);
         if (hostid_byip($row['ip'])!== null) {
             $message = "Такой ip - ".$row['ip']." уже есть в заббиксе";
             sendMessage($chat_id,$message);
          }
         else {
             if (ping($row['ip']) === "ok"){
                 $sw_type = $row['type'];
                 $query3 = "SELECT name, zab_group_id, zab_template_id FROM switch_type
                            WHERE id = '$sw_type'";
                 $result3 = mysqli_query($link_local,$query3);
                 if(mysqli_num_rows($result3) > 0){
                     while ($row3 = mysqli_fetch_array($result3)){
                         $type_name = $row3['name'];
			 $name =  remoove_slash($type_name).'_'.$row['ip'];
                         $groupid = $row3['zab_group_id'];
                         $templateid = $row3['zab_template_id'];
                         $message = host_create($name,$row['ip'], $groupid, $templateid, $row['mac'],$address_switch_en);
                         $query4 = "SELECT houses.brigada_inet FROM houses, switchs 
                                    WHERE switchs.house_id = houses.id AND switchs.id='$id_switch'";
                         $result4 = mysqli_query($link_nika,$query4);
                         if(mysqli_num_rows($result4) > 0) {  
                             while ($row4 = mysqli_fetch_array($result4)) {
                                 $brigada = $row4['brigada_inet'];
                                 $query5 = "INSERT INTO switches (name,ip,type,brigada,street,street_ru) 
                                            VALUES ('$type_name','$ip_switch','switch','$brigada','$address_switch_en','$address_switch_ru')";
                                 $result5 = mysqli_query($link_local,$query5);
                                 if($result5) { 
                                     $message1 = "и успешно добавлен в БД заббикс";
                                     sendMessage($chat_id,$message. ' ' .$message1);
                                 }
                                 else {
                                     $message1 = " но не добавлен в бд заббикс" . "Error message = ".mysqli_error($link_local);
                                     sendMessage($chat_id,$message. ' ' .$message1);
                                 }   
                             }
                         } else {
                             $message1 = " но ошибка поиска бригады которая обслуживает свитч" . "Error message = ".mysqli_error($link_nika);
                             sendMessage($chat_id,$message. ' ' .$message1);      
                         }
                     }                 
                 } else {
                 $message = "не найден заббикс-темплейт или группа для добавляемого свича ===>" . "Error message = ".mysqli_error($link_local);
                 sendMessage($chat_id,$message);
                 }        
             } else {
                 $message = "Новый свитч". ' ' .$ip_switch. ' ' .$address_switch_ru. ' ' . "но нет пинга";
                 sendMessage($chat_id,$message);
             }
     }
     $count_ip++;   
     }    
} elseif (mysqli_num_rows($result2) === 0) {
        $message = "В базе 10.0.0.38 нет новых свитчей";
        sendMessage($chat_id,$message);
} else {
        $message =  "Error message = ".mysqli_error($link_nika);
        sendMessage($chat_id,$message);
}      

$link_nika->close();
$link_local->close();

exit();
?>
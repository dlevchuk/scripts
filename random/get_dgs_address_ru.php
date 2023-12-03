#!/usr/bin/php -q
<?php

error_reporting(E_ALL); 
ini_set("display_errors", 0); 

$link2 = mysqli_connect("10.0.0.38", "", "", "");
    
if (mysqli_connect_errno()) {
    printf("Connection error: %s\n", mysqli_connect_error());
    exit();
}

mysqli_query ($link2,"set names utf8");

$switch_ip = '';
$id_podkat = '';

$agivars = array();
while (!feof(STDIN)) {
    $agivar = trim(fgets(STDIN));
    if ($agivar === '')
        break;

    $agivar = explode(':', $agivar);
    $agivars[$agivar[0]] = trim($agivar[1]);
}
extract($agivars);


$switch_ip = $_SERVER["argv"][1];  
$switch_ip = trim($switch_ip); /
$streets = array(); 
$ar = 0; 
$master = 0;
$cnt = 0;


function addAddress ($id_switchs){  
    global $link2;
    global $streets;
    global $ar;
    
    $query_hosts = "SELECT user
                        FROM hosts_all 
                        WHERE device = '$id_switchs'";
                        
    $result_hosts = mysqli_query($link2,$query_hosts);
    if(mysqli_num_rows($result_hosts) > 0){
        while ($row = mysqli_fetch_assoc($result_hosts)){                 
            $id_abon = $row['user'];                    
            $query_houses = "SELECT group_oper, brigada, id
                                FROM obzvonka 
                                WHERE id = '$id_abon'";

            $result_houses = mysqli_query($link2,$query_houses);
            while ($row3 = mysqli_fetch_assoc($result_houses)){
                                    
            $brigada = $row3['brigada'];                                             
            $streets["$ar"] = array(        
                        "brigada"=>"$brigada"
                        );            
            $ar++; 
            }                        
        }
    }    
    return $streets;  
}

function addDevice ($ip_device){  
    global $link2;
    global $streets;
    global $ar;
    

    $query_hosts = "SELECT d.address, o.brigada
                        FROM devices d
                        JOIN hosts_all h JOIN obzvonka o ON h.device = d.id AND h.user = o.id
                        WHERE cmts = '$ip_device'";
                        
    $result_hosts = mysqli_query($link2,$query_hosts);

    if(mysqli_num_rows($result_hosts) > 0){
        while ($row = mysqli_fetch_assoc($result_hosts)){                                  
            $brigada = $row['brigada'];                        
            $streets["$ar"] = array(         
                        "brigada"=>"$brigada"
                        );            
            $ar++; 
                                
        }
    } 

    return $streets; 
}

function arrayUnique($array, $preserveKeys = false){  
    $arrayRewrite = array();  
    $arrayHashes = array();  
    foreach($array as $key => $item) {  
        $hash = md5(serialize($item));  
        if (!isset($arrayHashes[$hash])) {  
            $arrayHashes[$hash] = $hash;  
            if ($preserveKeys) {  
                $arrayRewrite[$key] = $item;  
            } else {  
                $arrayRewrite[] = $item;  
            }  
        }  
    }  
    return $arrayRewrite;  
}   


function checkDgs($type){
        
    $res = 0;
    if ($type == 5 OR $type == 6 OR $type == 7 OR $type == 22 OR $type == 28 OR $type == 32 OR $type == 34) {
        $res = 1;
    }
    return $res;
}

function checkGpon($type){
        
    $res = 0;
    if ($type == 30 OR $type == 31) {
        $res = 1;
    }
    return $res;
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
   "#"=>"-","$"=>"","%"=>"","^"=>"","&"=>"","*"=>"",
   "("=>"",")"=>"","+"=>"","="=>"",";"=>"",":"=>"",
   "\'"=>"","\""=>"\"","~"=>"","`"=>"","?"=>"","/"=>"/",
   "\\"=>"\\","["=>"","]"=>"","{"=>"","}"=>"","|"=>""
  );

return strtr($input, $gost);
}


function array_icount_values($arr,$lower=true) { 
     $arr2=array(); 
     if(!is_array($arr['0'])){$arr=array($arr);} 
     foreach($arr as $k=> $v){ 
      foreach($v as $v2){ 
      if($lower==true) {$v2=strtolower($v2);} 
      if(!isset($arr2[$v2])){ 
          $arr2[$v2]=1; 
      }else{ 
           $arr2[$v2]++; 
           } 
    } 
    } 
    return $arr2; 
}


$query = "SELECT id, type, house_id
                FROM switchs
                WHERE ip = '$switch_ip'";

$result = mysqli_query($link2,$query);

if($result){
     while ($row = mysqli_fetch_assoc($result)) { 
             $id_dgs = $row['id']; 
             $type_id = $row['type']; 
             $house_id = $row['house_id']; 
     }
     
     $type = checkGpon($type_id);
    

     if ($type == 1) {

        $streets = addDevice ($switch_ip);

     }
     else{

          if(isset($id_dgs)){   
             
             $streets = addAddress ($id_dgs);

             $query_id = "SELECT id, ip
                            FROM switchs 
                            WHERE master = '$id_dgs'";                                                      
             $result_id = mysqli_query($link2,$query_id);
             if(mysqli_num_rows($result_id) > 0){ 
                    while ($row1 = mysqli_fetch_assoc($result_id)) { 
                        $id_switchs = $row1['id'];       
                        $ip_switchs = $row1['ip'];
                        $streets = addAddress ($id_switchs);
                        
                        $query_id_2 = "SELECT id, ip
                                        FROM switchs 
                                        WHERE master = '$id_switchs'";
                        $result_id_2 = mysqli_query($link2,$query_id_2);                        
                        if(mysqli_num_rows($result_id_2) > 0){    
                                while ($row2 = mysqli_fetch_assoc($result_id_2)){
                                        $id_switchs_2 = $row2['id']; //              
                                        $ip_switchs_2 = $row2['ip'];
                                        $streets = addAddress ($id_switchs_2);
                                }                                 
                        }
                                                                    
                    }
             }    
          }
          else{
             echo 'Switch not found!';
             exit();  
          } 
     }
  
 }
 else{
     echo 'query error!';
     exit();    
 }


if(!is_array($streets)){
    echo 'Error';
    exit; 
}
if(!$streets){
    echo 'brigades not found';
    exit;
}
else{

     if ($house_id >0) {
      
          $query1 = "SELECT s.id, s.address, s.house_id, t.name sname, h.street, h.house, st.name stname, t.id type_id
                     FROM switchs s
                     JOIN switch_type t JOIN houses h JOIN streets st ON  s.type = t.id AND s.house_id = h.id AND h.street = st.id
                     WHERE ip = '$switch_ip'";
          $result1 = mysqli_query($link2,$query1);
          $addres_switch = mysqli_fetch_assoc($result1);
          $type_dgs = $addres_switch['sname'];
          $stname = $addres_switch['stname'];
          $house = $addres_switch['house']; 

              $res = array_icount_values ($streets); 
              $cntt = max($res); 
              $key = array_search($cntt, $res); 
              $type_dgs = str_replace(" ", "_", $type_dgs);

              echo /*'Brigada - '.*/$key."\n";
              echo /*' - '.*/$type_dgs."\n";  
              echo /*' - '.*/$stname.' '.$house."\n";  
              echo /*' - '.*/$switch_ip."\n";  

     }
     else{
     
          $query1 = "SELECT s.address, t.name sname
                     FROM switchs s
                     JOIN switch_type t  ON  s.type = t.id
                     WHERE ip = '$switch_ip'";
          $result1 = mysqli_query($link2,$query1);
          $addres_switch = mysqli_fetch_assoc($result1);
          $type_dgs = $addres_switch['sname'];
          $adress_dgs = $addres_switch['address'];

              $res = array_icount_values ($streets); 
              $cntt = max($res); 
              $key = array_search($cntt, $res); 
              $type_dgs = str_replace(" ", "_", $type_dgs);

              echo /*'brigada - '.*/$key."\n";
              echo /*' - '.*/$type_dgs."\n";  
              echo /*' - '.*/$adress_dgs."\n";  
              echo /*' - '.*/$switch_ip."\n";  

     }

exit();

}    
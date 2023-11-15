<?php
//require "db.inc.php";

function addDevices($ip_address, $serial_number, $inst_address){ 
	
	$sql = 'INSERT INTO secur (ip, sn, address)
 				VALUES (?, ?, ?)';
 	if (!$stmt = mysqli_prepare($link1, $sql)){
 		return false;
 	}	
	mysqli_stmt_bind_param($stmt, "sss", $ip_address, $serial_number, $inst_address);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	return true;
}


function selectDevices(){ 

	global $link1;
	$sql = "SELECT * 
			    FROM secur
                GROUP BY id DESC";
    if(!$result = mysqli_query($link1, $sql)){
    	return false;	
    }
 	
   
}
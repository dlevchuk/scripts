<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
/***************************************************************************************/
require "inc/db.inc.php";
/***************************************************************************************/

if($_SERVER['REQUEST_METHOD']=='POST'){

	if (isset($_POST['ip_ping'])){  
		$ip_ping = $_POST["ip_ping"];
		$query_s = "SELECT *  
					    FROM secur_log
					    WHERE ip = '$ip_ping'
					    ORDER BY id DESC";

		$result_s = mysqli_query($link1, $query_s);

		echo "<table class='table'>";
		echo "<tr class='head'>";
		echo"
			<td>Temperature</td>
			<td>Uptime</td>
			<td>Work time</td>
			<td>Act type</td>
			<form action='index.php'>
			<td><button type='submit' onclick='clearlog()'>Close</button></td>
			</form>
		";
		echo '</tr>';
		while($row_s = mysqli_fetch_assoc($result_s)) {
		    
		    $id = $row_s['id'];
		    $ip = $row_s['ip'];
		    $temp = $row_s['temp'];
		    $uptime = $row_s['uptime'];
		    $time = $row_s['time'];
		    $error = $row_s['error'];
		    
		    echo "<tr class='cell'>";
		    	echo "
		    	<td>$temp</td>
		    	<td>$uptime</td>
		    	<td>$time</td>
		    	<td>$error</td>
		    	";
		    echo "</tr>";	
		}
		echo '</table>';
	}

	if (isset($_POST['ip_ping_alarm'])){

		$ip_ping_alarm = $_POST["ip_ping_alarm"];

		$query_s = "SELECT no_signal  
					    FROM secur
					    WHERE ip = '$ip_ping_alarm'";

		$result_s = mysqli_query($link1, $query_s);
		$row_s = mysqli_fetch_assoc($result_s);
		$no_signal = $row_s['no_signal'];

		if($no_signal == 0){
			$query_update = "UPDATE secur 
			    SET no_signal = '1'
			    WHERE ip = '$ip_ping_alarm'";
			$query_s = mysqli_query($link1, $query_update);    
			echo "st_red";    
		}
		elseif($no_signal == 1){
			
			$query_update = "UPDATE secur 
			    SET no_signal = '0'
			    WHERE ip = '$ip_ping_alarm'";
			$query_s = mysqli_query($link1, $query_update);    	   
			echo "st_green";    
		}		
	}
}
		$ip_ping_alarm = $_POST["ip_ping_alarm"];

		$query_s = "SELECT no_signal  
					    FROM secur
					    WHERE ip = '$ip_ping_alarm'";

		$result_s = mysqli_query($link1, $query_s);
		$row_s = mysqli_fetch_assoc($result_s);
		$no_signal = $row_s['no_signal'];

		if($no_signal == 0){
			$query_update = "UPDATE secur 
			    SET no_signal = '1'
			    WHERE ip = '$ip_ping_alarm'";
			$query_s = mysqli_query($link1, $query_update);    
			echo "st_red";    
		}
		elseif($no_signal == 1){
			
			$query_update = "UPDATE secur 
			    SET no_signal = '0'
			    WHERE ip = '$ip_ping_alarm'";
			$query_s = mysqli_query($link1, $query_update);    	   
			echo "st_green";    
		}
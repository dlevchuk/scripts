<?php
    require "inc/db.inc.php";
    require "inc/lib.inc.php";
?>
<!DOCTYPE html>
<head>
    <title>Web Control Pinger</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
    <script type="text/javascript" src="js/jquery-2.1.4.min.js" ></script>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 text-center">
            <h1>Controls</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <a href="index.php"><span class="glyphicon glyphicon-home"></span></a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-7">
            <table class="table table-striped table-hover table-condensed">
                <thead class="dark_head">
                    <tr>
                        <th>ID</th>
                        <th>IP</th>
                        <th>S/N</th>
                        <th>Adress</th>
                        <th>Door status</th>
                        <th>Temperature</th>
                        <th>Power</th>
                        <th>Signal</th>
                        <th>History</th>
                    </tr>
                </thead>
                <tbody>
<?php

$sql = "SELECT * 
            FROM secur
            GROUP BY id DESC";
if(!$result = mysqli_query($link1, $sql)){
    echo 'Error';  
}

while($row_s = mysqli_fetch_assoc($result)) {
    
    $id = $row_s['id'];
    $ip = $row_s['ip'];
    $sn = $row_s['sn'];
    $address = $row_s['address'];
    $door = $row_s['door'];
    $temp = $row_s['temp'];
    $knock = $row_s['knock'];
    $alarm = $row_s['no_signal'];
    $power = $row_s['power'];

if (isset($_GET["ping"])){

    if(isset($ip)){
        exec('ping '.$ip.' -c 1 -w 1 -q',$output, $status);
        if ($status==0){
            $sw = 'st_green';
        }
        else
            $sw = 'st_red';  
    }
}

echo "<tr class='text-center'>
        <td>$id</td>
        <td id='$id' data-ip='$ip'><a class='a_show ".$sw."' href='index.php?pn_id=".$id."'>$ip</a></td>
        <td>$sn</td>
        <td>$address</td>
";
        if ($door == 2) {
            echo "<td class='st_green' >Door is closed</td>";
        }
        elseif($door == 1){
            echo "<td class='st_red' >Door is opened</td>";
        }
        elseif($door == 0){
            echo "<td class='st_no-dt' >No signal</td>";
        }        
        else{
            echo 'error';
        }

        if ($temp == 1) {
            echo "<td class='st_red' >Temperature is high</td>";
        }
        elseif($temp == 2){
            echo "<td class='st_red' >Temperature is low</td>";
        }
        elseif($temp == 0){
            echo "<td class='st_green' >Temperature is ok</td>";
        }
        elseif($temp == 3){
            echo "<td class='st_no-dt' >No signal</td>";
        }           
        else{
            echo 'error';
        }

        if ($power == 1) {
            echo "<td class='st_green' >Power on</td>";
        }
        elseif($power == 2){
            echo "<td class='st_red' >No power</td>";
        }
        elseif($power == 0){
            echo "<td class='st_no-dt' >No signal</td>";
        }           
        else{
            echo 'error';
        }
        if ($alarm == 0) {
            echo "<td ><input type='button' class='ch-alarm st_green' id='$id' data-ip='$ip' value='On'></td>";
        }
        elseif($alarm == 1){
            echo "<td ><input type='button' class='ch-alarm st_red' id='$id' data-ip='$ip' value='Off'></td>";
        }
        elseif($alarm == 2){
            echo "<td ><input type='button' class='ch-alarm st_no-dt' id='$id' data-ip='$ip' value='No'></td>";
        }           
        else{
            echo 'ошибка';
        }
        // echo'
        // <td class="btn-group btn-toggle"> 
        //     <button class="btn btn-xs btn-default">ON</button>
        //     <button class="btn btn-xs btn-primary active">OFF</button>
        // </td>
        // ';


        echo "<td><input type='button' class='btn btn-primary btn-xs sh_log' id='$id' data-ip='$ip' value='Show'></td>";
    echo '</tr>';
}

?>

                </tbody>
            </table>
            <div class="row">
                <div class="col-xs-2">
                  <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#myModal">Add</button>  
                </div>
            </div>
        </div>
    </div>
    <br /> 
<?php

if($_SERVER['REQUEST_METHOD']=='GET'){

    if (isset($_GET["pn_id"])){
        $pn_id = $_GET["pn_id"];

        $query_s = "SELECT id, ip, sn, address  
                        FROM secur
                        WHERE id = '$pn_id'";

        $result_s = mysqli_query($link1, $query_s);

        while($row_s = mysqli_fetch_assoc($result_s)) {
            
            $id_pn = $row_s['id'];
            $ip_pn = $row_s['ip'];
            $sn_pn = $row_s['sn'];
            $address_pn = $row_s['address'];
             
        }

?>


<br />
<div class="row">
    <div class="col-md-3">
        <form method="post" action=".php" id="selectDevices">   
            <table class="table">
                <tbody>
                    <tr>
                        <td>Перейти:</td>
                        <td><a href="http://<?= $ip_pn?>" target="_blank"><?= $ip_pn?></a></td>
                    </tr>
                    <tr>
                        <td>IP ping:</td>
                        <td><input type="text" name="ip_pn" value="<?= $ip_pn?>" pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}" required></td>
                    </tr>
                    <tr>
                        <td>S/N:</td>
                        <td><input type="text" name="sn_pn" value="<?= $sn_pn?>" pattern="[0-9]{9}" maxlength="9" required></td>
                    </tr>
                    <tr>
                        <td>Адрес установки:</td>
                        <td><input type="text" name="address_pn" value="<?= $address_pn?>" required></td>
                    </tr>
                    <tr>
                    <td></td>
                    <td>
                        <input class="btn btn-primary btn-sm" type="submit" name="alter_pinger" value="Change">
                        <input class="btn btn-primary btn-sm" type="submit" name="delete_pinger" value="Delete" onclick="return confirm('Are you sure?')">
                    </td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" name="id_pinger" value="<?= $pn_id?>">
        </form> 
    </div>
</div> 
<?php
    } 
}
?>

<div class="modal fade" id="myModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="myModalLabel">Add</h4>
      </div>
      <div class="modal-body">
    <form role="form" id="ajax-form" class="form-horizontal">
      <div class="form-group has-feedback">
        <label for="ip" class="control-label col-xs-4">IP:</label>
        <div class="col-xs-5">
          <div class="form-group">        
            <input type="text" class="form-control" id="ip-ping" name="ip" pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}" required>
          </div>
        </div>
      </div>
      <div class="form-group has-feedback">
        <label for="sn" class="control-label col-xs-4">S/N:</label>
        <div class="col-xs-5">
          <div class="form-group">
            <input type="text" class="form-control" id="sn-ping" name="sn" pattern="[0-9]{9}" maxlength="9" required>
          </div>
        </div>
      </div>
      <div class="form-group has-feedback">
        <label for="address" class="control-label col-xs-4">Adress:</label>
        <div class="col-xs-5">
          <div class="form-group">         
            <input type="text" class="form-control" id="address-ping" name="address" required>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <input type="button" class="btn btn-default" data-dismiss="modal" value="Отмена">
        <button type="submit" id="save" class="btn btn-primary" >Add</button>
      </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div id="myModals" class="modal ">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">successfully added</h4>
      </div>
      <div class="modal-footer text_c">
        <button type="button" class="btn btn-success" data-dismiss="modal" onclick="window.location.reload()">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="show_res">
        </div>    
    </div>
</div>
</div>

    <script src="js/bootstrap.min.js"></script>
    <script src="js/idle-timer.js"></script>
    <script src="js/index.js"></script>
</body>
</html>
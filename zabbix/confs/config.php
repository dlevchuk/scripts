<?php

include_once 'confs/functions.php';

$link_local = mysqli_connect("", "", "", "");
$link_nika = mysqli_connect("", "", "", "");

if (mysqli_connect_errno()) {
    printf("Соединение не установлено: %s\n", mysqli_connect_error());
    exit();
}

mysqli_query ($link_local,"set names utf8");
mysqli_query ($link_nika,"set names utf8");

$chat_id = "";
$access_token = '';
$api = 'https://api.telegram.org/bot' . $access_token;

$zabbixUser = '';
$zabbixPass = '';
$zabbixUrl = '';
$header = array("Content-type: application/json-rpc");
$logininfo = '{"jsonrpc": "2.0","method":"user.login","params":{"user":"'.$zabbixUser.'","password":"'.$zabbixPass.'"},"auth": null,"id":0}';
$token = Curl($zabbixUrl,$header,$logininfo);
$token = $token->result;

?>
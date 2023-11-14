<?php
$start = microtime(true);
$url1 = "http://10.212.254.254/io_http.shtml";

function file_get_contents_curl($url) {
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_FAILONERROR, TRUE);
curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($curl);
curl_close($curl);
return $result;
}


$data = file_get_contents_curl($url1);
$data = strip_tags($data);

$re = '/(?<=Channel B Power:\r\n).*(?=&nbsp;dBm)/U';
preg_match($re, $data, $matches, PREG_OFFSET_CAPTURE, 0);


$re1 = '/(?<=Channel A Power:\r\n).*(?=&nbsp;dBm)/U';
preg_match($re1, $data, $matches1, PREG_OFFSET_CAPTURE, 0);

$a = $matches1[0][0]; 
$b = $matches[0][0];


$filename = "/home/last/scripts/parse_opt_sw.log";
 if (is_writeable($filename)) {
   $fh = fopen($filename, "a+");
   fwrite($fh, 'Channel A Power: '.$a. ' :: Channel B Power: ' .$b.";\r\n");
   fclose($fh);
   }
 else {
   print "Could not open $filename for writing";
   }


?>
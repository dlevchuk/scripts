#!/usr/bin/php
<?php

//php7.2-mbstring require
//*/1   *   *   *   *   /usr/bin/php /home/last/Scripts/ups_teleg.php

$chat_id = "";
$access_token = '';
$api = 'https://api.telegram.org/bot' . $access_token;

function sendMessage($chat_id, $message) {
     file_get_contents($GLOBALS['api'] . '/sendMessage?chat_id=' . $chat_id . '&text=' . urlencode($message));
 }

function transliterate($input){
 $gost = array(
 "<br>"=>"\n","&amp;"=>"&" );
 return strtr($input, $gost);
 } 

$hostname = '{:143}INBOX';
$username = '';
$password = '';

/* try to connect */
$inbox = imap_open($hostname,$username,$password) or die(sendMessage($chat_id,'Cannot connect to mail.odessa.tv for send alerts from new ups'));

/* grab emails */
$emails = imap_search($inbox,'UNSEEN', SE_UID);

/* if emails are returned, cycle through each... */
if($emails) {
	
	/* begin output var */
	$output = '';
	
	/* put the newest emails on top */
	rsort($emails);
	
	/* for every email... */
	foreach($emails as $email_number) {
		
		/* get information specific to this email */
		//$overview = imap_fetch_overview($inbox,$email_number,0);
		$message = imap_fetchbody($inbox,$email_number, 1, SE_UID);
	    $message = imap_qprint($message);
      
        $find_chr = mb_detect_encoding($message, "auto");
        
         if ($find_chr == 'ASCII'){
         	$message = base64_decode($message);
         }

        $message = transliterate($message);        
        $status = imap_setflag_full($inbox, $email_number, '\\Seen');
        
    $mess = $message;
    sendMessage($chat_id,$mess);

} 

/* close the connection */
imap_close($inbox);

?>
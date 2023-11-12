#!/usr/bin/php
<?php

$chat_id = "";
$access_token = '';
$api = 'https://api.telegram.org/bot' . $access_token;

function sendMessage($chat_id, $message) {
     file_get_contents($GLOBALS['api'] . '/sendMessage?chat_id=' . $chat_id . '&text=' . urlencode($message));
}

$hostname = '{:143}INBOX';
$username = '';
$password = '';

/* try to connect */
$inbox = imap_open($hostname,$username,$password) or die('Cannot connect ');


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
		
    //echo $email_number;

		/* get information specific to this email */
		$overview = imap_fetch_overview($inbox,$email_number,0);
		$message = imap_fetchbody($inbox,$email_number,2);
		
		/* output the email header information */
		//$output.= ($overview[0]->seen ? 'read' : 'unread');
		$output.= $overview[0]->subject;
		$output.= $overview[0]->from;
		$output.= $overview[0]->date;
		
				/* output the email body */
		$output.= $message.PHP_EOL; ;
		$status = imap_setflag_full($inbox, $email_number, '\\Seen');

	}
	
	$mess = htmlspecialchars("$output "); 
    sendMessage($chat_id,$mess);
} 

/* close the connection */
imap_close($inbox);

?>
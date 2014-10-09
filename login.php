<?php

require_once dirname(__FILE__).'/config.php';
require_once dirname(__FILE__).'/PHPMailer-master/PHPMailerAutoload.php';

$logFile = dirname( __FILE__ )."/log-".date('d-m-Y',time()).".txt";
$fh = fopen( $logFile, 'a' );

$authhost = "{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX";
$user = isset($_GET['u'])?$_GET['u']:"";
$pass = isset($_GET['p'])?$_GET['p']:"";

if( $user && $pass )
{
	if ($connection = imap_open( $authhost, $user, $pass ))
	{
	  echo "<h1>Connected to ".$user."</h1>\n";
	  fwrite( $fh, date('h:i:s',time()) . " : Connected to " . $user . "\n\n" );

	  $status = imap_status($connection, $authhost.'INBOX', SA_ALL);
	  $MC = imap_check($connection);
	  $num_mgs = $MC->Nmsgs; // Total Number of Messages found in Inbox

          // Loop to check first 50 emails in the Inbox
	  for( $i = $num_mgs; $i >= $num_mgs-50 ; $i-- )
	  {
		  $message_header = imap_fetchheader($connection, $i, FT_PREFETCHTEXT );
		  $headers = mail_parse_headers( $message_header );
		  $message_txt = imap_fetchbody( $connection, $i, 1.1 ); //imap_body ( $connection, $i, FT_INTERNAL );

		  $mhead = imap_header($connection, $i);
		  $subject = @$mhead->Subject;
	 	  $date = @$mhead->Date;
		  $from = $mhead->from[0]->mailbox.'@'.$mhead->from[0]->host;
		  $from_name = $mhead->from[0]->personal;

		  $header_str = '';
		  $isBlocked = false;	
		  $reason = '';
		 
		    // Check For Blocked Email Addresses
		    if( in_array( $from, $blocked_email_addresses ) ) {
			$isBlocked = true;
			$reason = 'Sent from blocked email address - '. $from.'<br />';
		    }
		    else
		    if( preg_match_all( "/\b(" .implode($blocked_email_addresses,"|"). ")\b/i", $message_txt, $match ) )
		    {
			$emailsFound = array_unique($match[0]);
			$emailsFound = implode( ', ', $emailsFound );

			$isBlocked = true;
			$reason = '<p> - Blocked email address involved - '. $emailsFound.'</p>';
		    }

		    // Check For Bad Keywords in Subject
		    $matches = array();
		    $matchFound = preg_match_all( "/\b(" . implode($blocked_keywords,"|") . ")\b/i", $subject, $matches );

		    if ($matchFound) {
		       $words = array_unique($matches[0]);
		       $bad_keywords_found = implode( ', ', $words );

		       $isBlocked = true;
		       $reason .= '<p> - Blocked keywords found in subject : '.$bad_keywords_found. '</p>';
		    }

                    // Check For Bad Keywords in Message Text
		    $matches = array();
		    $matchFound = preg_match_all( "/\b(" . implode($blocked_keywords,"|") . ")\b/i", $message_txt, $matches );

		    if ($matchFound) {
		       $words = array_unique($matches[0]);
		       $bad_keywords_found = implode( ', ', $words );

		       $isBlocked = true;
		       $reason .= '<p> - Blocked keywords found in message : '.$bad_keywords_found. '</p>';
		    }

		    if( $isBlocked )
		    {
			//echo '<p style="color:red;">BLOCKED !!!</p>'.$reason.'<br><br>';
			fwrite( $fh, "Blocked Email Found - Subject : " . $subject . "\n" );
			fwrite( $fh, "Reason : " . $reason . "\n" );

			foreach( $headers as $key => $val )
                          $header_str .= '<b>' . $key . ':</b> ' . (is_array($val)?json_encode($val):$val) . '<br>';

			$header_str .= '<br>'.json_encode($mhead).'<br>';

			$html = '<p style="color:red;">Blocked !!!</p>'.$reason.'<br><br>
	                         <b><u>Headers</u> :</b><br>'.$header_str.'<br><br>
                                 <b><u>Message</u> :</b><br>'.$message_txt;

		        $mail = new PHPMailer;
			//$mail->SMTPDebug = 3;                               // Enable verbose debug output
			$mail->isSMTP();                                      // Set mailer to use SMTP
			$mail->Host = $email_settings['smtp_host'];           // Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                               // Enable SMTP authentication
			$mail->Username = $email_settings['smtp_username'];   // SMTP username
			$mail->Password = $email_settings['smtp_password'];   // SMTP password
			$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
			$mail->Port = 587;                                    // TCP port to connect to

			$mail->From = $email_settings['from_email_address'];
			$mail->FromName = $email_settings['from_name'];

			if( !empty($to_email_addresses) )
			  foreach($to_email_addresses as $K )
                             if( $K[0] && $K[1] )			    
				$mail->addAddress( $K[0], $K[1] );

			$mail->addReplyTo( $email_settings['reply_to_email_address'] , $email_settings['reply_to_name'] );

			$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
			$mail->isHTML(true);                                  // Set email format to HTML

			$mail->Subject = $email_settings['email_subject'].' from - '.$user;
			$mail->Body    = $html;

			if(!$mail->send())
                        {                    
			    echo "Message could not be sent. Mailer Error: " .  $mail->ErrorInfo ."<br>";
			    fwrite( $fh, "Can't forward email. Mail Error : " . $mail->ErrorInfo . "\n" );
			}
			else
			{
			    echo 'Message has been sent<br>';
			    fwrite( $fh, "Mail Sent.\n" );
			}

                        if( imap_delete( $connection, $i ) )
                        {
			   echo '<br> Message Deleted - '.$i.' !!!';
			   fwrite( $fh, "MESSAGE DELETED SUCCESSFULLY !!!\n\n" );
			}
			else
			{
			   echo '<br> Can\'t delete the message - '.$i.' !!!';
			   fwrite( $fh, "MESSAGE COULD NOT BE DELETED FROM THE INBOX !!!\n\n" );
			}
		    }
	  }

          imap_expunge( $connection );
	  imap_close( $connection );
	  fclose($fh);
	  exit;
	}
	else
	{
	  echo ("Can't connect: " . imap_last_error())."<h1>FAIL!</h1>\n";
	  fwrite( $fh, date('h:i:s',time()) . " : Could not connect to - " . $user . "\n\n" );
	  fclose($fh);
	  exit;
	}
}
else
{
	echo "No username or password passed !";
	fwrite( $fh, date('h:i:s',time()) . " : No username or password passed !\n\n" );
	fclose($fh);
	exit;
}

function mail_parse_headers($headers)
{

    $headers=preg_replace('/\r\n\s+/m', '',$headers);
    $headers=trim($headers)."\r\n"; /* a hack for the preg_match_all in the next line */
    preg_match_all('/([^: ]+): (.+?(?:\r\n\s(?:.+?))*)?\r\n/m', $headers, $matches);
    foreach ($matches[1] as $key =>$value) $result[$value]=$matches[2][$key];
    return($result);
}

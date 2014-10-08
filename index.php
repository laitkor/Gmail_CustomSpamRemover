<?php

ini_set('max_execution_time', 0);
require_once dirname(__FILE__).'/config.php';

$logFile = dirname( __FILE__ )."/log-".date('d-m-Y',time()).".txt";
$fh = fopen( $logFile, 'a' );

if( !empty($check_inbox_of) )
{
	foreach( $check_inbox_of as $key )
        {
            if( $key[0] && $key[1] )
            {
		    $text = "==============================================================\n";
		    $text .= date('h:i:s',time()) . " - Connecting : " . $key[0] . "\n";
		    fwrite( $fh, $text );

		    $url = $base_url."/login.php?u=".$key[0]."&p=".$key[1];
		    $c = curl_init();		     
		    $options = array(
			CURLOPT_RETURNTRANSFER => true ,
			CURLOPT_URL => $url
		    );		     
		    curl_setopt_array($c, $options);
		    $output = curl_exec($c);
		     
		    print_r( $output );

		    echo date('h:i:s') . "<br>";
                    sleep(120);
                    echo date('h:i:s') . "<br>";
            }
	}
}

fclose($fh);
exit;

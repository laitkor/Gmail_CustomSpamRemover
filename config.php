<?php

// 1- BASE URL
$base_url = 'http://localhost/Gmail_CustomSpamRemover/';

// 2- SMTP & EMAIL Settings
$email_settings = array(
	"smtp_username" => "ENTER_YOUR_SMTP_USERNAME",
        "smtp_password" => "ENTER_YOUR_SMTP_PASSWORD",
        "smtp_host" => "smtp.gmail.com", // SMTP HOST
        "smtp_port" => "587", // SMTP PORT
        "email_subject" => "Email Deleted",
	"from_name" => "Laitkor Admin",
	"from_email_address" => "admin@laitkor.com",
        "reply_to_name" => "No Reply",
        "reply_to_email_address" => "noreply@laitkor.com"
);

// 3 - Blocked Keywords Array
$blocked_keywords = array( "qwertyuiop", "asdfghjkl", "zxcvbnm" );

// 4 - Blocked Email Addresses Array
$blocked_email_addresses = array( "abc@laitkor.com", "xyz@laitkor.com" );

// 5 - Email Addresses wih Names, to whom the blocked emails are to be forwarded
$to_email_addresses = array();
$to_email_addresses[] = array( "email_1@laitkor.com", "Name 1" );
$to_email_addresses[] = array( "email_2@laitkor.com", "Name 2" );

// 6 - Email Addresses & App Passwords of all users, whose Inboxes are to be scanned
$check_inbox_of = array();
$check_inbox_of[] = array( "echeck_user_1@laitkor.com", "app_pass_1" );
$check_inbox_of[] = array( "echeck_user_2@laitkor.com", "app_pass_3" );
$check_inbox_of[] = array( "echeck_user_3@laitkor.com", "app_pass_2" );

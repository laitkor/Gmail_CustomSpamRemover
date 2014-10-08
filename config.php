<?php

// BASE URL of your script
$base_url = 'http://localhost/Gmail_CustomSpamRemover/';

// SMTP & EMAIL Settings
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

// Blocked Keywords Array
$blocked_keywords = array( "qwertyuiop", "asdfghjkl", "zxcvbnm" );

// Blocked Email Addresses Array
$blocked_email_addresses = array( "abc@laitkor.com", "xyz@laitkor.com" );

// Email Addresses & Name of Users, whom a copy of deleted messages are to be forwarded
$to_email_addresses = array();
$to_email_addresses[] = array( "email_1@laitkor.com", "Name 1" );
$to_email_addresses[] = array( "email_2@laitkor.com", "Name 2" );

// Email Addresses & APP Password of users, whose INBOXs are to checked
$check_inbox_of = array();
$check_inbox_of[] = array( "echeck_user_1@laitkor.com", "app_pass_1" );
$check_inbox_of[] = array( "echeck_user_2@laitkor.com", "app_pass_3" );
$check_inbox_of[] = array( "echeck_user_3@laitkor.com", "app_pass_2" );

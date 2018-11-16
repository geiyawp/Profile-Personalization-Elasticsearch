<?php
	session_start();
	require_once "vendor/autoload.php";
	$go = new Google_Client();	// initialize API client
	$go->setAuthConfig('inc/client_credentials.json'); // set client creadentials in "client_credential.json" file
	$go->setApplicationName("Some Search Engine"); // set application name
	$go->setRedirectUri("http://localhost/skrispi/g-callback.php"); //set redirect URI
	$go->addScope("https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/plus.login"); // set scope
?>

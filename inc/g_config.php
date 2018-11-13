<?php
	session_start();
	require_once "vendor/autoload.php";
	$go = new Google_Client();
	$go->setAuthConfig('inc/client_credentials.json');
	// $go->setClientId("699972064996-iucnlgd72q0paoppjcn8nvddq55fr59q.apps.googleusercontent.com");
	// $go->setClientSecret("THKHfX8tdwLB1fyff1vwYM7u");
	$go->setApplicationName("Skripsi Search Engine");
	$go->setRedirectUri("http://localhost/skrispi/g-callback.php");
	$go->addScope("https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/plus.login");
?>

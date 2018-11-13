<?php
	require_once "inc/g_config.php";
	unset($_SESSION['access_token']);
	$go->revokeToken();
	session_destroy();
	header('Location: google_login.php');
	exit();
?>
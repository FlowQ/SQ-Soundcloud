<?php
	session_start();
	require_once 'Services/Soundcloud.php';
	//require_once 'config.php';
	require_once 'config_dev.php';
	
	$_SESSION['lol'] = '12';
	// create client object with app credentials
	$client = new Services_Soundcloud(APP_ID, APP_SECRET, CALLBACK_URL);

	// redirect user to authorize URL
	header("Location: " . $client->getAuthorizeUrl());

?>
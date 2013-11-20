<?php

	require_once 'Services/Soundcloud.php';
	if(strpos($_SERVER['HTTP_HOST'], 'localhost')!==false) {
	    require_once ('Config/config_dev.php'); //dev
	 } else {
	    require_once ('Config/config.php'); //prod
	 }  
	
	// create client object with app credentials
	$client = new Services_Soundcloud(APP_ID, APP_SECRET, CALLBACK_URL);

	// redirect user to authorize URL
	header("Location: " . $client->getAuthorizeUrl());

?>
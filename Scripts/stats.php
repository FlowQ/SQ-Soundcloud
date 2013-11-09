<?php
	require_once 'Services/Soundcloud.php';
//	require_once 'config_dev.php';
	require_once 'config.php';

	// create a client object with your app credentials
	$client = new Services_Soundcloud(APP_ID, APP_SECRET, CALLBACK_URL);

	// find all sounds of buskers licensed under 'creative commons share alike'
	$me = json_decode($client->get('users/32869948.json'));

    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $bdd = new PDO(DSN, DB_USERNAME, DB_PASSWORD, $pdo_options);

	$stat = $bdd->prepare("INSERT INTO Stats (NbeFollowers, NbeFollowing, NbeLikes, NbeNewLikes, NbeLikesAttente, NbeRejected, ArtisteFavoris, BestRatio) VALUES (:f, :fg, :nbel, :nbenl, :nbela, :nber, :af, :br)");
	$nbenlR = $bdd->prepare("SELECT count(SCid) from FLikes where AddDate > NOW() - INTERVAL 1 DAY");
	$nbelaR = $bdd->prepare("SELECT count(SCid) from FLikes");
	$nberR = $bdd->prepare("SELECT count(SCid) from Rejected");
	$afR = $bdd->prepare("SELECT SCuid from FLikes GROUP BY SCuid ORDER BY COUNT(SCid) DESC");
	$brR = $bdd->prepare("SELECT Liked/Count as Ratio from FLikes WHERE Liked > 1 and Count > 1 ORDER BY Ratio ASC");
	$nbenlR->execute();
	$nbenl = $nbenlR->fetch(PDO::FETCH_COLUMN, 0);
	$nbelaR->execute();
	$nbea = $nbelaR->fetch(PDO::FETCH_COLUMN, 0);
	$nberR->execute();
	$nber = $nberR->fetch(PDO::FETCH_COLUMN, 0);
	$afR->execute();
	$af = $afR->fetch(PDO::FETCH_COLUMN, 0);
	$brR->execute();
	$br = $brR->fetch(PDO::FETCH_COLUMN, 0);

print_r($me);
    $stat->execute(array('f' => $me->followers_count, 'fg' => $me->followings_count, 'nbel' => $me->public_favorites_count, 'nbenl' => $nbenl, 'nbela' => $nbea, 'nber' => $nber, 'af' => $af, 'br' => $br));

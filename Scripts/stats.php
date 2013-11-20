<?php
	 if(strpos($_SERVER['HTTP_HOST'], 'localhost')!==false) { 
	    require_once ('../Config/config_dev.php'); //dev
	  }  else {
	    chdir('/opt/app/current/SQ/Flow/Scripts');
	    require_once ('../Config/config.php'); //prod
	  }  
	require_once '../Services/Soundcloud.php';
	// create a client object with your app credentials
	$client = new Services_Soundcloud(APP_ID, APP_SECRET, CALLBACK_URL);

	if(isset($_GET['tsuid'])) {
		calcStats(intval($_GET['tsuid']), $client);
		//header('Location:  ../dashboard.php');
	} else {
	    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	    $bdd = new PDO(DSN, DB_USERNAME, DB_PASSWORD, $pdo_options);
	    $listIDreq = $bdd->prepare("SELECT TSuid FROM Users");
	    $listIDreq->execute();
	    $listID = $listIDreq->fetchall(PDO::FETCH_COLUMN, 0);



		foreach ($listID as $id) {
			echo 'XXX';
			calcStats($id, $client);
		}
	}

	function calcStats($id, $client) {

	    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	    $bdd = new PDO(DSN, DB_USERNAME, DB_PASSWORD, $pdo_options);

	    $myIDreq = $bdd->prepare("SELECT SCuid FROM Users WHERE TSuid = $id");
	    $myIDreq->execute();
	    $myID = $myIDreq->fetch(PDO::FETCH_COLUMN, 0);


		$me = json_decode($client->get("users/$myID.json"));



		$stat = $bdd->prepare("INSERT INTO Stats (SCid, TSuid, Username, NbeFollowers, NbeFollowing, NbeLikes, NbeNewLikes, NbeLikesAttente, NbeRejected, ArtisteFavoris, BestRatio) VALUES (:id, :tsuid, :uname, :f, :fg, :nbel, :nbenl, :nbela, :nber, :af, :br)");
		$nbenlR = $bdd->prepare("SELECT count(SCid) from FLikes where Liked > 1 AND Count > 1 AND TSuid = $id AND AddDate > NOW() - INTERVAL 1 DAY");
		$nbelaR = $bdd->prepare("SELECT count(SCid) from FLikes WHERE TSuid = $id AND Count > 1 AND Liked > 1");
		$nberR = $bdd->prepare("SELECT count(SCid) from Rejected WHERE TSuid = $id");
		$afR = $bdd->prepare("SELECT SCuid from FLikes WHERE TSuid = $id GROUP BY SCuid ORDER BY COUNT(SCid) DESC");
		$brR = $bdd->prepare("SELECT Liked/Count as Ratio from FLikes WHERE TSuid = $id AND Liked > 1 and Count > 1 ORDER BY Ratio ASC");
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

	    $stat->execute(array('id' => $myID, 'tsuid' => $id, 'uname' => $me->username, 'f' => $me->followers_count, 'fg' => $me->followings_count, 'nbel' => $me->public_favorites_count, 'nbenl' => $nbenl, 'nbela' => $nbea, 'nber' => $nber, 'af' => $af, 'br' => $br));
	}
?>
<?php
  session_start();
require_once 'Services/Soundcloud.php';

	if(strpos($_SERVER['HTTP_HOST'], 'localhost')!==false) {
		require_once ('Config/config_dev.php'); //dev
	echo "dev'";
	} else {
		require_once ('Config/config.php'); //prod
	} 

// create client object and set access token
$client = new Services_Soundcloud(APP_ID, APP_SECRET, CALLBACK_URL);
$token = $_GET['token'];

//echo $token;
$client->setAccessToken($token);

//gets Followersfrom API
function GapiFollowers($client) {
	$page_size=50;
	$i=0;
	$followers = array();
	// gets all the followers users
	do //to get all the pages of results
	{
		$nextF = json_decode($client->get('me/followers.json', array('order' => 'username', 'limit' => $page_size, 'offset' => $i*$page_size)));
		$followers = array_merge($followers, $nextF);
		$i++;
	}while(count($nextF)==50);

	$listeFollowersId = array();
	foreach($followers as $fol) {
		$cel = array($fol->id, $fol->username);
		array_push($listeFollowersId, $cel);
	}
	
	return $listeFollowersId;
}

//gets Following from API
function GapiFollowing($client) {
	$page_size=50;
	$i=0;
	$following = array();
	// gets all the following users
	do //to get all the pages of results
	{
		$nextF = json_decode($client->get('me/followings.json', array('order' => 'username', 'limit' => $page_size, 'offset' => $i*$page_size)));
		$following = array_merge($following, $nextF);
		$i++;
	}while(count($nextF)==50);

	$listeFollowersId = array();
	foreach($following as $fol) {
		$cel = array($fol->id, $fol->username);
		array_push($listeFollowersId, $cel);
	}
	
	return $listeFollowersId;
}

//stores follow ing/ers to DBB
function storeFollow($liste, $listeId, $follower) {
	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	$bdd = new PDO(DSN, DB_USERNAME, DB_PASSWORD, $pdo_options);
	$req = $bdd->prepare('INSERT INTO Follow (TSuid, SCuid, Artist, Follower) VALUES (:tsuid, :scuid, :artist, :follower)');
	foreach($liste as $li) {
		if(!in_array(intval($li[0]), $listeId)) {
			$req->execute(array('tsuid' => $_SESSION['TSuid'], 'scuid' => $li[0], 'artist' => $li[1], 'follower' => $follower));
		}
	}
}

//gets flikes from API
function GapiFLikes($followId, $client) {
	$i=0;
	$page_size=200;
	$duree_max = 10; //limits all the tracks to 10 minutes
	$likes = array();
	do //to get all the pages of results
	{
		$nextS = json_decode($client->get("users/$followId/favorites.json", array('limit' => $page_size, 'offset' => $i*$page_size, 'order' => 'id', 'duration[to]' => $duree_max*60*1000)));
		$likes = array_merge($likes, $nextS);
		$i++;
	}while(count($nextS)==$page_size);


	$listeLikes = array();
	foreach($likes as $li) {

		//like = TSid - SCid - SCuid - Title - Artist - Count - Liked - AddDate
			$liked = 0;
			if(isset($li->favoritings_count))
			{ $liked=$li->favoritings_count;}
			$cel = array($li->id, $li->user->id, $li->title, $li->user->username, $followId, $liked);
			array_push($listeLikes, $cel);
	} 

	return $listeLikes;
}


//gets follow posted songs from API
/*function GapiFSongs($followId, $client) {
	$i=0;
	$page_size=200;
	$duree_max = 10; //limits all the tracks to 10 minutes
	$max_retreive = 2000;
	$likes = array();
	do //to get all the pages of results
	{
		$nextS = json_decode($client->get("users/$followId/tracks.json", array('limit' => $page_size, 'offset' => $i*$page_size, 'order' => 'created_at', 'duration[to]' => $duree_max*60*1000)));
		$likes = array_merge($likes, $nextS);
		$i++;
	}while(count($nextS)==$page_size && count($likes)<=$max_retreive);


	$listeLikes = array();
	foreach($likes as $li) {

		//like = TSid - SCid - SCuid - Title - Artist - Count - Liked - AddDate
			$liked = 0;
			if(isset($li->favoritings_count))
			{ $liked=$li->favoritings_count;}
			$cel = array($li->id, $li->user->id, $li->title, $li->user->username, $followId, $liked);
			array_push($listeLikes, $cel);
	} 

	return $listeLikes;
}*/

//stores fLikes to DBB
function storeFLikes($liste, $follow) {
	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	$bdd = new PDO(DSN, DB_USERNAME, DB_PASSWORD, $pdo_options);
	$insert = $bdd->prepare('INSERT INTO FLikes (TSid, TSuid, SCid, SCuid, Title, Artist, Count, FromU, Liked, AddDate) VALUES (null, :tsuid, :scid, :scuid, :title, :artist, :count, :fromu, :liked, DEFAULT)');
	$update = $bdd->prepare('UPDATE FLikes SET Count = Count + 1, FromU = :fromu WHERE SCid = :scid AND TSuid = :tsuid');
	$fromu = $bdd->prepare('SELECT FromU FROM FLikes WHERE SCid = :scid AND TSuid = :tsuid ');
	$listeId = GdbbLikesId();

	$listeIdF = GdbbFLikesId ();

	foreach($liste as $cel) {

		if(!in_array(intval($cel[0]), $listeId)) {
			if(!in_array(intval($cel[0]), $listeIdF)) {
				$insert->execute(array('tsuid' => $_SESSION['TSuid'], 'scid' => $cel[0], 'scuid' => $cel[1], 'title' => $cel[2], 'artist' => $cel[3],  'count' => 1, 'fromu' => $cel[4], 'liked' => $cel[5])); 
				array_push($listeIdF, $cel[0]); }
			else { 
				if(!in_array(intval($follow), GbddFromU($cel[0]))) {
					$fromu->execute(array('scid' => $cel[0], 'tsuid' => $_SESSION['TSuid']));
					$ins = $fromu->fetch(PDO::FETCH_COLUMN, 0).'-'.$follow;				
					$update->execute(array('tsuid' => $_SESSION['TSuid'], 'scid' => $cel[0], 'fromu' => $ins)); 
				}
			}
		}
	}
}

//gets Users with the sound from DBB
function GbddFromU($SCid) {
	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	$bdd = new PDO(DSN, DB_USERNAME, DB_PASSWORD, $pdo_options);

	$requete = $bdd->prepare("SELECT FromU FROM FLikes WHERE SCid = :scid AND TSuid = :tsuid");
	$requete->execute(array('scid' => $SCid, 'tsuid' => $_SESSION['TSuid']));

	$listeFromU = $requete->fetch(PDO::FETCH_COLUMN, 0);
	return array_map('intval', explode('-',$listeFromU));
}

//gets my likes from API
function GapiLikes($client) {
	$i=0;
	$page_size=200;
	$likes = array();
	do //to get all the pages of results
	{
		$nextS = json_decode($client->get("me/favorites.json", array('limit' => $page_size, 'offset' => $i*$page_size, 'order' => 'id')));
		$likes = array_merge($likes, $nextS);
		$i++;
	}while(count($nextS)==$page_size);

	$listeLikes = array();
	foreach($likes as $li) {
		//like = TSid - SCid - SCuid - Title - Artist - AddDate
			$liked = 0;
			if(isset($li->favoritings_count))
			{ $liked=$li->favoritings_count;}
			$cel = array($li->id, $li->user->id, $li->title, $li->user->username);
			array_push($listeLikes, $cel);
	}
	return $listeLikes;
}

//stores my likes to DBB
function storeLikes($liste, $listeId) {
	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	$bdd = new PDO(DSN, DB_USERNAME, DB_PASSWORD, $pdo_options);
	
	$req = $bdd->prepare('INSERT INTO Likes (TSid, TSuid, SCid, SCuid, Title, Artist, AddDate) VALUES (null, :tsuid, :scid, :scuid, :title, :artist, DEFAULT)');
	foreach($liste as $cel) {
		if(!in_array(intval($cel[0]), $listeId)) {
			$req->execute(array('tsuid' => $_SESSION['TSuid'], 'scid' => $cel[0], 'scuid' => $cel[1], 'title' => $cel[2], 'artist' => $cel[3]));
		}
	}
}

//gets my likes from DBB
function GdbbLikesId() {
	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	$bdd = new PDO(DSN, DB_USERNAME, DB_PASSWORD, $pdo_options);
	$requete = $bdd->prepare("SELECT SCid FROM Likes WHERE TSuid = :tsuid");
	$requete->execute(array('tsuid' => $_SESSION['TSuid']));
	$listeId = $requete->fetchAll(PDO::FETCH_COLUMN, 0);
	return array_map('intval', $listeId);
}

//gets follow ID from DBB
function GdbbFollowId() {
	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	$bdd = new PDO(DSN, DB_USERNAME, DB_PASSWORD, $pdo_options);

	$requete = $bdd->prepare("SELECT SCuid FROM Follow WHERE TSuid = :tsuid");
	$requete->execute(array('tsuid' => $_SESSION['TSuid']));

	$listeId = $requete->fetchAll(PDO::FETCH_COLUMN, 0);
	return array_map('intval', $listeId);
}

//stopwatch
function writeSW($time, $message) {
	$end = microtime(true);
	file_put_contents('logTime.txt', $message.' - '.($end-$time).' sec'."\n", FILE_APPEND);
}

//gets fLikes from DBB
function GdbbFLikesId() {
	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	$bdd = new PDO(DSN, DB_USERNAME, DB_PASSWORD, $pdo_options);

	$requete = $bdd->prepare("SELECT SCid FROM FLikes WHERE TSuid = :tsuid");
	$requete->execute(array('tsuid' => $_SESSION['TSuid']));

	$listeId = $requete->fetchAll(PDO::FETCH_COLUMN, 0);
	return array_map('intval', $listeId);
}

//fontion à lancer
function main($client) {
	/*
	1. Récupérer mes likes
	2. Les stocker en base
	3. Récupérer les followers
	4. Les stocker en base
	5. Récupérer leur likes
	6. Récupérer de la base la liste des rejected
	7. Stocker les likes en base
	*/

$log = fopen('log.txt', 'w');
$start = microtime(true);

fwrite($log, "Debut : _SESSION['TSuid'] = ".$_SESSION['TSuid']."\n");
	$totalLikes = 0;
	$id = json_decode($client->get('me.json'))->id; //0.

$p=print_r($id, true);
fwrite($log, $p."\n");
	$listLikes = GapiLikes($client); //1.
fwrite($log, 'Durée get mes likes API : '.(microtime(true)-$start)."\n");
	$listIdLikes = GdbbLikesId(); //Récupère les ID des likes avant stockage
fwrite($log, 'Durée get mes likes DBB : '.(microtime(true)-$start)."\n");
	storeLikes($listLikes, $listIdLikes);
fwrite($log, 'Durée put mes likes DBB : '.(microtime(true)-$start)."\n"); //2.

	$listeFollowing = GapiFollowing($client);
fwrite($log, 'Durée get following API : '.(microtime(true)-$start)."\n"); //3.
	$listIdFollow = GdbbFollowId(); //Récupère les ID des follow avant stockage
fwrite($log, 'Durée get follow DBB : '.(microtime(true)-$start)."\n");
	storeFollow($listeFollowing, $listIdFollow, 0); 
fwrite($log, 'Durée put follow DBB : '.(microtime(true)-$start)."\n"); //4.

	$listeFollowers = GapiFollowers($client);
fwrite($log, 'Durée get followers API : '.(microtime(true)-$start)."\n"); //3'.
	$listIdFollow = GdbbFollowId(); //Récupère les ID des follow avant stockage
fwrite($log, 'Durée get follow DBB : '.(microtime(true)-$start)."\n");
	storeFollow($listeFollowers, $listIdFollow, 1); 
fwrite($log, 'Durée put follow DBB : '.(microtime(true)-$start)."\n"); //4'.


	$listIdFollow = GdbbFollowId(); //Récupère les ID des follow pour les likes
fwrite($log, 'Liste Following : '.count($listIdFollow)."\n");
	$i=1;
	$total = 0;
	foreach($listIdFollow as $following) {
		$lStart = microtime(true);
		$listeFLikes = GapiFLikes($following, $client); //5.
		if (count($listeFLikes)!=0) {
			storeFLikes($listeFLikes, $following); //7.
		}
		fwrite($log, "\tF".($i++).' : '.$following.' - Likes : '.count($listeFLikes). ' - Duration : '.(microtime(true)-$lStart)."\n");
		$total+=count($listeFLikes);
	}

/*
	$sons=0;
	foreach($listIdFollow as $following) {
		$lStart = microtime(true);
		$listeFLikes = GapiFSongs($following, $client); //5.
		if (count($listeFLikes)!=0) {
			storeFLikes($listeFLikes, $following); //7.
		}
		fwrite($log, "\tS".($i++).' : '.$following.' - Sons : '.count($listeFLikes). ' - Duration : '.(microtime(true)-$lStart)."\n");
		$sons+=count($listeFLikes);
	}
*/ //on ignore les chansons postées par les users pour le moment car trop lourd a calculer
fwrite($log, 'Durée totale calcul : '.(microtime(true)-$start)."\n");
fclose($log);



}
main($client);



	


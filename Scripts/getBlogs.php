<?php
	if(strpos($_SERVER['HTTP_HOST'], 'localhost')!==false) { 
		require_once ('../Config/config_dev.php'); //dev
	}  else {
	chdir('/opt/app/current/SQ/Flow/Scripts');
    require_once ('../Config/config.php'); //prod
	}

    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $bdd = new PDO(DSN, DB_USERNAME, DB_PASSWORD, $pdo_options);
    

    $insSon = $bdd->prepare("INSERT INTO Blog (SCid, Blog) VALUES (:scid, :blog)");
    $isDB = $bdd->prepare("SELECT count(TSsid) FROM Blog WHERE SCid = :scid");
    $lastIdQuery = $bdd->prepare("SELECT SCid FROM Blog ORDER BY TSsid DESC LIMIT 1");
    $lastIdQuery->execute();
    


	//initialisation
	$srcUrl='http://www.musicmakesmyday.fr';
	$blogName = "MMMD";
	$lastId = $lastIdQuery->fetch(PDO::FETCH_COLUMN, 0);
	if(!$lastId)
		$lastId=0;


	$html = file_get_contents($srcUrl);
	
	$dom = new DOMDocument();
	@$dom->loadHTML($html);

	// grab all the on the page
	$xpath = new DOMXPath($dom);

	//finding the a tag
	$hrefs = $xpath->evaluate("/html/body//iframe");

	//Loop to display all the links
	for ($i = 0; $i < $hrefs->length; $i++) {
	       $href = $hrefs->item($i);
	       $url = $href->getAttribute('src');
	       //Filter the null links
	       if($url!='#')
	       {
	       		$query = (parse_url($url));
	       		$arg = explode("%2F", $query['query']);
	       		$id = explode("&", $arg[4]);
	       		$isDB->execute(array('scid' => $id[0]));
	       		$count = $isDB->fetch(PDO::FETCH_COLUMN, 0);
	       		if(!$count) {
		       		$insSon->execute(array('scid' => $id[0], 'blog' => $blogName));
		       	}
	       }
	}
?>
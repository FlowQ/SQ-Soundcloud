<?php
  session_start();
  error_reporting(E_ALL);
  if(strpos($_SERVER['HTTP_HOST'], 'localhost')!==false) {
    require_once ('Config/config_dev.php'); //dev
    echo "dev'";
  } else {
    require_once ('Config/config.php'); //prod
  }  
  require_once 'Services/Soundcloud.php';
  $client = new Services_Soundcloud(APP_ID, APP_SECRET, CALLBACK_URL);
  if(isset($_GET['code'])) {
    callback($client);
  }
  $client->setAccessToken($_SESSION['token']['access_token']);

  function callback($client) {
    $code = $_GET['code'];
    $client->setCurlOptions(array(
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false
    ));
    $_SESSION['token'] = $client->accessToken($code);
  }

  function getAction($client) {
    $index = 1;
    if(isset($_POST['choice'])&&isset($_POST['id'])) {
        $id = $_POST['id'];
        $choice = $_POST['choice'];

        if ($choice == 'like') {
          like($id, $client);
        }
        else {
          dislike($id);
        }
    }
    if(isset($_GET['no'])) {
      $index = $_GET['no'];
    }
    return $index;
  }  

  function like($id, $client) {
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $bdd = new PDO(DSN, DB_USERNAME, DB_PASSWORD, $pdo_options);
    $del = $bdd->prepare("DELETE FROM FLikes WHERE SCid = $id"); 
    $del->execute();
    $client->put('me/favorites/'.$id, $id);
  }

  function dislike($id) {
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $bdd = new PDO(DSN, DB_USERNAME, DB_PASSWORD, $pdo_options);
    $son = $bdd->prepare("SELECT SCid, Title, Artist, Count, Liked FROM FLikes WHERE SCid = $id");
    $son->execute();
    $res = $son->fetch();
    $del = $bdd->prepare("DELETE FROM FLikes WHERE SCid = $id"); 
    $reject = $bdd->prepare("INSERT INTO Rejected (SCid, Title, Artist, Count, Liked) VALUES (:scid, :t, :a, :c, :l)");
    $reject->execute(array('scid' => $res[0], 't' => $res[1], 'a' => $res[2], 'c' => $res[3], 'l' => $res[4]));

    $del->execute();
  }

  function getStats()
  {
    $myID = '32869948';
  
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $bdd = new PDO(DSN, DB_USERNAME, DB_PASSWORD, $pdo_options);
    $stats = $bdd->prepare("SELECT Username, NbeFollowers, NbeFollowing, NbeLikes, NbeLikesAttente, NbeRejected FROM Stats WHERE SCid = $myID ORDER BY Date DeSC LIMIT 1");
    $stats->execute();
    $result = $stats->fetch();

    return $result;
  }

  function getSC($client, $id) {
      $son = json_decode($client->get("tracks/$id.json"));
      return $son;
  }

  function getTS($id) {
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $bdd = new PDO(DSN, DB_USERNAME, DB_PASSWORD, $pdo_options);
    $req = $bdd->prepare("SELECT Count  FROM FLikes WHERE SCid = $id"); //ordre par nombre de like
    $req->execute();
    $count = $req->fetch();
    $req = $bdd->prepare("SELECT Liked/Count FROM FLikes WHERE SCid = $id"); //ordre par ratio
    $req->execute();
    $ratio = $req->fetch();
    $res = array(intval($ratio[0]), $count[0]);
    return $res; 
  }

  $index = getAction($client);
  $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
  $bdd = new PDO(DSN, DB_USERNAME, DB_PASSWORD, $pdo_options);
  
//  $list = $bdd->prepare('SELECT SCid, Title, Artist  FROM FLikes ORDER BY Count DESC LIMIT 10'); //ordre par nombre de like
  $list = $bdd->prepare("SELECT SCid as Divi, Title, Artist FROM FLikes   WHERE Liked>0 and Count > 1 order by Liked/Count asc limit 10"); //ordre par ratio
  $list->execute();
  $tenSongs = $list->fetchall();
  $result = $tenSongs[$index-1];
  $son = '<iframe width="100%" height="140px" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/'.$result[0].'"></iframe>';

  $titre = $result[1].' - '.$result[2];
?>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="Data/icon.png">

    <title>TopSounds for Soundcloud</title>

    <!-- Bootstrap core CSS -->
    <link href="Data/dist/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="Data/dist/offcanvas.css" rel="stylesheet">

    <!-- my CSS -->
    <link href="Data/topsounds.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../docs-assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
    <div class="navbar navbar-fixed-top navbar-inverse" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">TopSounds</a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">Dashboard</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
        </div><!-- /.nav-collapse -->
      </div><!-- /.container -->
    </div><!-- /.navbar -->

    <div class="container">

      <div class="row row-offcanvas row-offcanvas-right">

        <div class="col-xs-12 col-sm-9">
          <p class="pull-right visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
          </p>
          <div class="jumbotron">
            <?php echo $son; ?>
            <div class="half">
              <p><?php echo $titre; ?></p>
            </div>
            <form action="dashboard.php" method="post" class="like">
              <input type="hidden" name="choice" value="dislike">
              <input type="hidden" name="id" value="<?php echo $result[0]; ?>">
              <input type="image" src="Data/dislike.png" alt="Submit" class="like">
            </form>
            <form action="dashboard.php" method="post" class="like">
              <input type="hidden" name="choice" value="like">
              <input type="hidden" name="id" value="<?php echo $result[0]; ?>">
              <input type="image" src="Data/like.png" alt="Submit" class="like">
            </form>
          </div>  
          <div class="row">
            <div class="col-6 col-sm-6 col-lg-4">
              <h2>Sur Soundcloud</h2>
              <?php
                $scinfos = getSC($client, $result[0]);
                echo "<p>";
                print_r($scinfos->user->username);
                echo " a publié ce son qui a été joué $scinfos->playback_count fois, liké $scinfos->favoritings_count et commenté $scinfos->comment_count fois.";
              ?>
              <p><a class="btn btn-default" TARGET=_BLANK href="<?php echo $scinfos->permalink_url; ?>" role="button">Sur Soundcloud &raquo;</a></p>
            </div><!--/span-->
            <div class="col-6 col-sm-6 col-lg-4">
              <h2>Sur Top Sounds</h2>
              <?php
                $tsinfos = getTS($result[0]);
                echo "<p>Ce son a été liké $tsinfos[1] fois par les utilisateurs que vous suivez et qui vous suivent. De plus il présente un ratio de $tsinfos[0] entre les likes de vos connexion et ceux de Soundcloud</p>";
              ?>
              <p><a class="btn btn-default" TARGET=_BLANK href="about.php#calcul" role="button">Mode de calcul &raquo;</a></p>
            </div><!--/span-->
            <div class="col-6 col-sm-6 col-lg-4">
              <h2><?php $infos = getStats(); echo $infos['Username']; ?></h2>
              <?php
              //Username, NbeFollowers, NbeFollowing, NbeLikes, NbeLikesAttente, NbeRejected
                echo "<p>Aujourd'hui vous suivez $infos[2] utilisateurs et $infos[1] autres vous suivent en retour. Vous avez aimé $infos[3] chansons différentes, $infos[4] sont en attente sur Top Sounds et vous en avez déjà rejetées $infos[5].</p>" ;
              ?>
              <p><a class="btn btn-default" href="https://soundcloud.com/" TARGET=_BLANK role="button">Voir mon profil &raquo;</a></p>
            </div><!--/span-->
          </div><!--/row-->
        </div><!--/span-->

        <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">
          <div class="classement">Classement par Ratio</div>
          <div class="list-group">
            <?php 
              $i=1;
              foreach ($tenSongs as $son) {
                if($i == $index) {
                  //probleme ici car voir quel onglet actif
                  echo '<a href="dashboard.php?no='.$i.'" class="list-group-item active">'.$i.' - '.$son[1].'</a>';
                }
                else {
                  echo '<a href="dashboard.php?no='.$i.'" class="list-group-item">'.$i.' - '.$son[1].'</a>';
                }
                $i++;
              }
            ?>
          </div>
        </div><!--/span-->
      </div><!--/row-->

      <hr>

      <footer>
        <p>&copy; Top Sounds 2013</p>
      </footer>

    </div><!--/.container-->



    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="Data/dist/js/bootstrap.js"></script>
    <script src="Data/dist/offcanvas.js"></script>
  </body>
</html>

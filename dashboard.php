<?php
  session_start();
//  error_reporting(E_ALL);
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
  if(!isset($_SESSION['token'])) {
    header('Location:  index.php');
  } else {

  }
  if(strpos($_SERVER['HTTP_HOST'], 'localhost')!==false) {
    require_once ('Config/config_dev.php'); //dev
  } else {
    require_once ('Config/config.php'); //prod
  }  
  $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
  $bdd = new PDO(DSN, DB_USERNAME, DB_PASSWORD, $pdo_options);

  require_once 'Services/Soundcloud.php';
  $client = new Services_Soundcloud(APP_ID, APP_SECRET, CALLBACK_URL);
  if(isset($_GET['code'])) {
    callback($client, $bdd);
  }
  $client->setAccessToken($_SESSION['token']['access_token']);

  function whoIs($client, $bdd) {
    $me = json_decode($client->get('me/'));
    $req = $bdd->prepare("SELECT TSuid FROM Users WHERE SCuid = $me->id");
    $req->execute();
    $tsuid = $req->fetch(PDO::FETCH_COLUMN, 0);
    if($tsuid) {
      return $tsuid;
    } else {
      $ins = $bdd->prepare("INSERT INTO Users (SCuid, Username, Name) VALUES (:id, :un, :n)");
      $ins->execute(array('id' => $me->id, 'un' => $me->permalink, 'n' => $me->username));
      $id = $bdd->prepare("SELECT TSuid FROM Users WHERE SCuid = :id");
      $id->execute(array('id' => $me->id));
      $_SESSION['TSuid'] = $id->fetch(PDO::FETCH_COLUMN, 0);
      header('Location:  new.php?id='.$_SESSION['TSuid']);
      return $tsuid;
    }
  }

  function callback($client, $bdd) {
    $code = $_GET['code'];
    $client->setCurlOptions(array(
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false
    ));
    $_SESSION['token'] = $client->accessToken($code);
    $_SESSION['TSuid'] = whoIs($client, $bdd);
  }

  function getAction($client, $bdd) {
    $index = 1;
    if(isset($_POST['choice'])&&isset($_POST['id'])) {
        $id = $_POST['id'];
        $choice = $_POST['choice'];

        if ($choice == 'like') {
          like($id, $client, $bdd);
        }
        else {
          dislike($id, $bdd);
        }
    }
    if(isset($_GET['no'])) {
      $index = $_GET['no'];
    }
    return $index;
  }  

  function like($id, $client, $bdd) {
    $del = $bdd->prepare("DELETE FROM FLikes WHERE SCid = $id AND TSuid = ".$_SESSION['TSuid']); 
    $del->execute();
    $client->put('me/favorites/'.$id, $id);
  }

  function dislike($id, $bdd) {
    $son = $bdd->prepare("SELECT SCid, Title, Artist, Count, Liked FROM FLikes WHERE SCid = $id");
    $son->execute();
    $res = $son->fetch();
    $del = $bdd->prepare("DELETE FROM FLikes WHERE SCid = $id AND TSuid = ".$_SESSION['TSuid']); 
    $reject = $bdd->prepare("INSERT INTO Rejected (SCid, TSuid, Title, Artist, Count, Liked) VALUES (:scid, :tsuid, :t, :a, :c, :l)");
    $reject->execute(array('scid' => $res[0], 'tsuid' => $_SESSION['TSuid'], 't' => $res[1], 'a' => $res[2], 'c' => $res[3], 'l' => $res[4]));
    $del->execute();
  }

  function getStats($bdd) {  
    $stats = $bdd->prepare("SELECT Username, NbeFollowers, NbeFollowing, NbeLikes, NbeLikesAttente, NbeRejected FROM Stats WHERE TSuid = ".$_SESSION['TSuid']." ORDER BY Date DeSC LIMIT 1");
    $stats->execute();
    $result = $stats->fetch();

    return $result;
  }

  function getSC($client, $id) {
      $son = json_decode($client->get("tracks/$id.json"));
      return $son;
  }

  function getTS($id, $bdd) {
    $req = $bdd->prepare("SELECT Count FROM FLikes WHERE SCid = $id AND TSuid = ".$_SESSION['TSuid']); //ordre par nombre de like
    $req->execute();
    $count = $req->fetch();
    $req = $bdd->prepare("SELECT Liked/Count FROM FLikes WHERE SCid = $id AND TSuid = ".$_SESSION['TSuid']); //ordre par ratio
    $req->execute();
    $ratio = $req->fetch();
    $res = array(intval($ratio[0]), $count[0]);
    return $res; 
  }

  //gets a mix ranking of the songs
  //ordered half by likes and half by ratio 
  function getSongsMixRank($bdd) {
    $count = $bdd->prepare("SELECT SCid FROM FLikes WHERE Liked>0 AND Count > 1 AND TSuid = ".$_SESSION['TSuid']." ORDER BY Count DESC "); //ordre par nombre de like
    $ratio = $bdd->prepare("SELECT SCid FROM FLikes WHERE Liked>0 AND Count > 1 AND TSuid = ".$_SESSION['TSuid']." ORDER BY Liked/Count ASC"); //ordre par ratio

    $count->execute();
    $ratio->execute();

    $listRatio = $ratio->fetchall(PDO::FETCH_COLUMN, 0);
    $listCount = $count->fetchall(PDO::FETCH_COLUMN, 0);

    $i=1;
    $arrScore = array();
    $arrId = array();
    foreach($listCount as $son) {
      $index = array_search(intval($son), $listRatio);
      $score = ($index+1+$i)/2;

      array_push($arrId, $son);
      array_push($arrScore, $score);
      $i++;
    }

    array_multisort($arrScore, $arrId);
    $result = array();
    if(count($arrId)>9) {
      for($i=0;$i<10;$i++) {
        $son = $bdd->prepare("SELECT SCid, Title, Artist FROM FLikes WHERE SCid = $arrId[$i]");
        $son->execute();
        $result[] = $son->fetch();
      }
    } else {
      foreach ($arrId as $val) {
        $son = $bdd->prepare("SELECT SCid, Title, Artist FROM FLikes WHERE SCid = $val");
        $son->execute();
        $result[] = $son->fetch();
      }
    }

    return $result;
  }

  $index = getAction($client, $bdd);
  
  $tenSongs = getSongsMixRank($bdd);
  if (count($tenSongs)>0) {
    $result = $tenSongs[$index-1];
    $son = '<iframe width="100%" height="140px" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/'.$result[0].'"></iframe>';
    $titre = $result[1].' - '.$result[2];
  }

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
            <li class="active"><a href="">Dashboard</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
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
            <?php if(isset($son)) echo $son; ?>
            <div class="half">
              <p><?php if(isset($titre)) echo $titre; else echo "Vous n'avez pas encore synchronisé votre Soundcloud, </br>Cliquez sur Mettre à Jour, puis raffraichissez dans une minute"; ?></p>
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
                if(isset($result)) {
                  $scinfos = getSC($client, $result[0]);
                  echo "<p>";
                  print_r($scinfos->user->username);
                  echo " a publié ce son qui a été joué $scinfos->playback_count fois, liké $scinfos->favoritings_count et commenté $scinfos->comment_count fois.";
                }
              ?>
              <p><a class="btn btn-default" TARGET=_BLANK href="<?php echo $scinfos->permalink_url; ?>" role="button">Sur Soundcloud &raquo;</a></p>
            </div><!--/span-->
            <div class="col-6 col-sm-6 col-lg-4">
              <h2>Sur Top Sounds</h2>
              <?php
                if(isset($result)) {
                  $tsinfos = getTS($result[0], $bdd);
                  echo "<p>Ce son a été liké $tsinfos[1] fois par les utilisateurs que vous suivez et qui vous suivent. De plus il présente un ratio de $tsinfos[0] entre les likes de vos connexion et ceux de Soundcloud</p>";
                }
              ?>
              <p><a class="btn btn-default" TARGET=_BLANK href="about.php#calcul" role="button">Mode de calcul &raquo;</a></p>
            </div><!--/span-->
            <div class="col-6 col-sm-6 col-lg-4">
              <h2><?php $infos = getStats($bdd); echo $infos['Username']; ?></h2>
              <?php
              //Username, NbeFollowers, NbeFollowing, NbeLikes, NbeLikesAttente, NbeRejected
                echo "<p>Aujourd'hui vous suivez $infos[2] utilisateurs et $infos[1] autres vous suivent en retour. Vous avez aimé $infos[3] chansons différentes, $infos[4] sont en attente sur Top Sounds et vous en avez déjà rejetées $infos[5].</p>" ;
              ?>
              <p><a class="btn btn-default" href="https://soundcloud.com/" TARGET=_BLANK role="button">Voir mon profil &raquo;</a></p>
            </div><!--/span-->
          </div><!--/row-->
        </div><!--/span-->

        <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">
          <div class="maj"><a href="javascript:launchMAJ()" TARGET=_BLANK ><img src="Data/update.png" />  Mettre à jour<a></div>
          </br>
          <div class="classement">Classement Mix</div>
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

    <script type="text/javascript">
      function getXMLHttpRequest() {
        var xhr = null;

        if (window.XMLHttpRequest || window.ActiveXObject) {
          if (window.ActiveXObject) {
            try {
              xhr = new ActiveXObject("Msxml2.XMLHTTP");
            } catch(e) {
              xhr = new ActiveXObject("Microsoft.XMLHTTP");
            }
          } else {
            xhr = new XMLHttpRequest(); 
          }
        } else {
          alert("Pas d'Ajax, dommage!");
          return null;
        }

        return xhr;
      }

      var token = "<?php echo $_SESSION['token']['access_token']; ?>";
      <?php 
        if(isset($_GET['new']) && $_GET['new'] == 'true') {
          echo "launchMAJ(false);";
        }
      ?>


      //asynchronous call to the update process
      function launchMAJ(id) {
        var req = getXMLHttpRequest();
        req.onreadystatechange = function() {
          if (req.readyState == 4 && (req.status == 200 || req.status == 0)) {
          }
        };
        req.open("GET", "me.php?token=" + token, true);
        req.send(null);
        if(typeof(id)==='undefined')
          alert("Mise à jour en cours");
      }
    </script>
  </body>
</html>

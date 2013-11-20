<?php 
  session_start();
  ini_set('display_errors',1);
  ini_set('display_startup_errors',1);
  error_reporting(-1); $_SESSION['TSuid'] = $_GET['id']; 


  exec ("php Scripts/stats.php 2> /dev/null > /dev/null &"); 
    //exec ("php me.php ".$_SESSION['token']['access_token']." ".$_SESSION['TSuid']." 2> /dev/null > /dev/null &"); 
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
            <li><a href="dashboard.php">Dashboard</a></li>
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
          <div class="jumbotron" style="padding-bottom: 40px">
            	<p style="padding-top: 15px">Vous êtes nouveau sur Top Sounds. </p>
            	<a href="javascript:launchMAJ()" style="padding-bottom: -5px">Cliquer ici pour lancer la recherche</a>
          </div>  
         </div>
        <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">
          <div class="classement"></div>
          <div class="list-group">
          	<p class="list-group-item active">Icons from <a href="http://icons8.com/">Icon8</a></p>
          	<p class="list-group-item active">ECE Paris - 2013</p>
          	<p class="list-group-item active"><b>Florian Quattrocchi</b></p>
          	<p class="list-group-item active"><i>Software Quality</i></p>
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
          alert("Pas d'Ajax, tu fais pas le ménage, dommage!");
          return null;
        }

        return xhr;
      }

      var token = "<?php echo $_SESSION['token']['access_token']; ?>";
      var id = "<?php echo $_SESSION['TSuid']; ?>";
      //launchStats();
      //asynchronous call to the update process
      function launchStats() {
        var req = getXMLHttpRequest();
        req.onreadystatechange = function() {
          if (req.readyState == 4 && (req.status == 200 || req.status == 0)) {
          }
        };
        req.open("GET", "me.php?token=" + token, true);
        req.send(null);
      }
      function launchMAJ(){
        var req = getXMLHttpRequest();
        req.onreadystatechange = function() {
          if (req.readyState == 4 && (req.status == 200 || req.status == 0)) {
          }
        };
        req.open("GET", "Scripts/stats.php?tsuid=" + id, true);
        req.send(null);
        setTimeout(function(){ document.location.href="dashboard.php?new=true"; }, 1500);
      }
    </script>
  </body>
</html>
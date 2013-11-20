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
            <li class="active"><a href="">About</a></li>
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
            	<p style="padding-top: 15px">Top Sounds est une application vous permettant de trouver les chansons que vous êtes le plus à même d'apprécier sur Soundcloud. </p>
          </div>  
          <p>L'algorithme de recommandation de Top Sounds analyse les chansons que vos pairs ont appréciées et détermine ainsi une sélection de titres à vous proposer.</p>
          <p>Le top 10 de ces chansons est affiché dans la partie droite du site, et celle sélectionnée est disponible à l'écoute dans la partie centrale.</p>
          <p>Vous pouvez aimer cette chanson, ce qui aura pour effet de l'ajouter à la liste des likes de votre profil Soundcloud et la fera disparaitre de Top Sounds.</p>
          <p>Ou au contraire vous pouvez ne pas apprécier la chanson et le signaler afin qu'elle ne vous soit plus proposée de nouveau.</p>
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
  </body>
</html>
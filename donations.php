<?php
	/*error_reporting(E_ALL);
	ini_set('display_errors', 1);*/
	session_start();
?>
<!DOCTYPE html>
<html lang="it">
	<head>
		<meta charset="utf-8" />
		<title>SWEgo - Donazioni</title>
		<meta name="title" content="SWEgo - Donazioni" />
		<meta name="description" content="Aiuta i creatori di SWEgo con una donazione" />
		<meta name="keywords" content="SWEgo, Donazione, Paypal" />
		<meta name="author" content="Luca Bertolini, Marco Bonolo, Mauro Carlin, Nicola Tintorri" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<link rel="icon" href="image/favicon.png" />
		<link rel="stylesheet" type="text/css" href="css/mobile.css" />
		<link rel="stylesheet" type="text/css" href="css/desktop.css" />
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="js/nav.js"></script>
	</head>
  <body>
		<header>
			<a href="index.php"><h1 id="logo" lang="en">SWEgo</h1></a>
			<a id="skipNavbar" href="#breadcrumb">Premi per saltare il menu</a>
			<div id="mobileMenu" class="hideButton">
				<span></span>
				<span></span>
				<span></span>
			</div>
			<nav>
				<?php
					if(empty($_SESSION)) {
				?>
					<ul>
						<li><a href="index.php">HOMEPAGE</a></li>
						<li><a href="contacts.php">CONTATTI</a></li>
						<li><a href="login.php">ACCEDI</a></li>
						<li><a href="registration.php">REGISTRATI</a></li>
						<li class="here">DONAZIONI</li>
						<li><a href="faq.php">F.A.Q.</a></li>
					</ul>

				<?php
					} else {
				?>
					<ul>
						<li><a href="insertSource.php">FONTI</a></li>
						<li><a href="insertActor.php">ATTORI</a></li>
						<li><a href="viewRequirement.php">REQUISITI</a></li>
						<li lang="en"><a href="viewUsecase.php">USE CASE</a></li>
						<li><a href="viewTracking.php">TRACCIAMENTO</a></li>
						<li><a href="user.php">PROFILO</a></li>
						<li><a href="contacts.php">CONTATTI</a></li>
						<li class="here">DONAZIONI</li>
						<li><a href="faq.php">F.A.Q.</a></li>
					</ul>
				<?php
					}
				?>
			</nav>
		</header>
		<div id="breadcrumb" class="noSpaceHeader">
			<p>Ti trovi in: DONAZIONI</p>
		</div>
		<div id="content">
			<div id="intro">
        <p>Il progetto SWEgo ci e' costato sudore e fatica. Se lo state usando e vi piace potete offrirci un caffe'.</p>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" id="donazione">
          <input type="hidden" name="cmd" value="_s-xclick">
          <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
          <input type="image" src="https://www.paypalobjects.com/it_IT/IT/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal è il metodo rapido e sicuro per pagare e farsi pagare online.">
          <img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
        </form>
    </div>
		</div>
		<footer>
			<span lang="en">SWEgo &copy;</span>
			<a href="mapLogin.html">Mappa del sito</a>
			<img src="image/html5valid.png" class="valid" alt="HTML5 Valid!"/>
			<img src="image/cssvalid.png" class="valid" alt="CSS Valid!"/>
		</footer>
	</body>
</html>

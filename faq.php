<?php
	/*error_reporting(E_ALL);
	ini_set('display_errors', 1);*/
	session_start();
?>
<!DOCTYPE html>
<html lang="it">
	<head>
		<meta charset="utf-8" />
		<title>SWEgo - FAQ</title>
		<meta name="title" content="SWEgo - FAQ" />
		<meta name="description" content="Risposte ai quesiti più frequenti." />
		<meta name="keywords" content="SWEgo, FAQ, Domande, Frequenti, Quesiti, Risposte" />
		<meta name="author" content="Luca Bertolini, Marco Bonolo, Mauro Carlin, Nicola Tintorri" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<link rel="icon" href="image/favicon.png" />
		<link rel="stylesheet" type="text/css" href="css/mobile.css" />
		<link rel="stylesheet" type="text/css" href="css/desktop.css" />
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="js/nav.js"></script>
		<script src="js/features.js"></script>
	</head>
  <body>
		<header>
			<h1 id="logo" lang="en">SWEgo</h1>
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
						<li><a href="donations.php">DONAZIONI</a></li>
						<li class="here">F.A.Q.</li>
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
						<li><a href="donations.php">DONAZIONI</a></li>
						<li class="here">F.A.Q.</li>
					</ul>
				<?php
					}
				?>
			</nav>
		</header>
		<div id="breadcrumb" class="noSpaceHeader">
			<p>Ti trovi in:<abbr title="Frequently Asked Questions"> FAQ</abbr></p>
		</div>
		<div id="content">
			<div id="intro">
        <p>In questa pagina troverai le risposte ai quesiti più frequenti. Se non trovi quelli che cerchi usa la pagina <a href="contacts.php">Contatti</a> per comunicarcelo.</p>
      </div>
			<dl id="faqQuestion">
				<dt>Ogni membro del gruppo dovr&agrave; farsi un account?</dt>
				<dd>No. Tutto il gruppo deve accedere ad un unico account, così si potrà lavorare simultaneamente sugli stessi dati.</dd>
				<dt>Perch&eacute; quando genera la documentazione non trovo le immagini dei diagrammi UML?</dt>
				<dd>Una volta che hai generato la documentazione segui questa procedura:
					<ol>
						<li>apri il documento di testo seguendo il path <span class="code">cartella_download/PlantUML/documento_di_testo</span>;</li>
						<li>copia il codice;</li>
						<li>visita il sito <a href="www.plantuml.com">www.plantuml.com</a>;</li>
						<li>incolla la porzione di codice del documento di testo nella area di testo;</li>
						<li>salva il diagramma creato.</li>
					</ol>
				</dd>
				<dt>Quali pacchetti devo includere per compilare i file latex?</dt>
			</dl>
    </div>
		<footer>
			<span lang="en">SWEgo &copy;</span>
			<a href="mapLogin.html">Mappa del sito</a>
			<img src="image/html5valid.png" class="valid" alt="HTML5 Valid!"/>
			<img src="image/cssvalid.png" class="valid" alt="CSS Valid!"/>
		</footer>
	</body>
</html>

<?php
	session_start();
	
	if(array_key_exists("type", $_SESSION) && $_SESSION["type"] == "admin") {
		header("Location: admin.php");
		exit();
	}
	
	$page = file_get_contents("template/contacts.html");
	if(empty($_SESSION)) {
		$menu = file_get_contents("template/menuNoLogin.html");
		$page = str_replace(':map:', 'mapVisitor.html', $page);
	}
	else {
		$menu = file_get_contents("template/menuLogin.html");
		$page = str_replace(':map:', 'mapLogin.html', $page);
	}
	$menu = str_replace('<li><a href="contacts.php">CONTATTI</a></li>', '<li class="here">CONTATTI</li>', $menu);
	$page = str_replace(':menu:', $menu, $page);
	echo $page;
?>
<?php
	session_start();
	
	$page = file_get_contents("template/contacts.html");
	if(empty($_SESSION))
		$menu = file_get_contents("template/menuNoLogin.html");
	else
		$menu = file_get_contents("template/menuLogin.html");
	$menu = str_replace('<li><a href="contacts.php">CONTATTI</a></li>', '<li class="here">CONTATTI</li>', $menu);
	$page = str_replace(':menu:', $menu, $page);
	echo $page;
?>
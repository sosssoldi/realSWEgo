<?php
	session_start();
	
	$page = file_get_contents("template/index.html");
	if(empty($_SESSION)) {
		$menu = file_get_contents("template/menuNoLogin.html");
		$menu = str_replace('<li><a href="index.php">HOMEPAGE</a></li>', '<li class="here">HOMEPAGE</li>', $menu);
	}
	else
		$menu = file_get_contents("template/menuLogin.html");
	$page = str_replace(':menu:', $menu, $page);
	echo $page;
?>
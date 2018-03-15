<?php
	session_start();
	$_SESSION["id"] = "";
	$_SESSION["groupname"] = "";
	$_SESSION["projectname"] = "";
	$_SESSION["type"];
	unset($_SESSION["id"]);
	unset($_SESSION["groupname"]);
	unset($_SESSION["projectname"]);
	unset($_SESSION["type"]);
	unset($_SESSION);
	session_destroy();
	header("Location: index.php");
?>
<?php
	session_start();
	if(empty($_SESSION)) {
		header("Location: ../index.html");
		exit();
	}

	if(!empty($_SESSION)) {
		header("Location: ../user.php");
		exit();
	}
?>
<?php
	/*error_reporting(E_ALL);
	ini_set('display_errors', 1);*/
	session_start();
	include_once "php/Database/Database.php";
	include_once "php/DAO/UsecaseDAO.php";
	include_once "php/Object/Object.php";
	include_once "php/Object/Usecase.php";

	if(empty($_SESSION)) {
		header("Location: index.php");
		exit();
	}

	if(array_key_exists("type", $_SESSION) && $_SESSION["type"] == "admin") {
		header("Location: admin.php");
		exit();
	}

	if(empty($_POST)) {
		render_page();
	} else {
		if(!Usecase::check_tracking_input($_POST)) {
			render_page($_POST);
		} else {
			$_POST = Usecase::parse_input($_POST);
			$usecaseDAO = new UsecaseDAO();
			$usecaseDAO->track($_POST);
			render_page('insert');
		}
	}
?>

<?php
	function render_page($data = null) {
		$page = file_get_contents("template/insertTracking.html");
		$usecaseDAO = new UsecaseDAO();
		echo $usecaseDAO->adjustTrackingForm($page, $data, $_SESSION["id"]);
	}
?>

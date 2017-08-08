<?php
	/*error_reporting(E_ALL);
	ini_set('display_errors', 1);*/
	session_start();
	include_once "php/Database/Database.php";
	include_once "php/DAO/DAO.php";
	include_once "php/DAO/UsecaseDAO.php";
	include_once "php/Object/Object.php";
	include_once "php/Object/Usecase.php";

	if(empty($_SESSION))
		header("Location: index.html");

	if(empty($_GET) || !array_key_exists('id', $_GET) || $_GET["id"] == "")
		header("Location: viewTracking.php");

	$usecaseDAO = new UsecaseDAO();
	if(!$usecaseDAO->getUsecase($_GET["id"], $_SESSION["id"])) {
		header("Location: viewTracking.php");
		exit();
	}

	if(empty($_POST)) {
		render_page($_GET["id"]);
	} else {
		if(!Usecase::check_tracking_input($_POST)) {
			render_page($_GET["id"]);
		} else {
			$_POST = Usecase::parse_input($_POST);
			$usecaseDAO = new UsecaseDAO();
			$usecaseDAO->track($_POST);
			header("Location: viewTracking.php");
		}
	}
?>

<?php
	function render_page($id = null) {
		$page = file_get_contents("template/modifyTracking.html");
		$usecaseDAO = new UsecaseDAO();
		$rs = $usecaseDAO->getUsecase($id, $_SESSION["id"]);
		if($rs) {
			$usecase = $rs[0];
			echo $usecaseDAO->fillTrackingForm($page, $usecase, $_SESSION["id"]);
		} else 
			header("Location: viewTracking.php");
	}
?>

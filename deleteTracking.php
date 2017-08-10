<?php
	/*error_reporting(E_ALL);
	ini_set('display_errors', 1);*/
	session_start();
	include_once "php/Database/Database.php";
	include_once "php/DAO/DAO.php";
	include_once "php/DAO/UsecaseDAO.php";
	include_once "php/Object/Object.php";
	include_once "php/Object/Usecase.php";

	if(empty($_SESSION)) {
		header("Location: index.html");
		exit();
	}

	if(array_key_exists("type", $_SESSION) && $_SESSION["type"] == "admin") {
		header("Location: admin.php");
		exit();
	}

	if(empty($_GET) || !array_key_exists('id', $_GET) || $_GET['id'] == "") {
		header("Location: viewTracking.php");
		exit();
	}

	$usecaseDAO = new UsecaseDAO();
	if(!$usecaseDAO->getUsecase($_GET["id"], $_SESSION["id"])) {
		header("Location: viewTracking.php");
		exit();
	}

	if(empty($_POST)) {
		render_page($_GET['id']);
	} else {
		if(array_key_exists('confirm', $_POST)) {
			if($_POST["confirm"] == "si") {
				$usecaseDAO = new UsecaseDAO();
				$usecaseDAO->deleteTracking($_GET["id"]);
			}
			header("Location: viewTracking.php");
		} else
			render_page($_GET['id']);
	}
?>

<?php
	function render_page($id = null) {
		$page = file_get_contents("template/deleteTracking.html");
		$usecaseDAO = new UsecaseDAO();
		$usecase = $usecaseDAO->getUsecase($id, $_SESSION["id"]);
		if($usecase) {
			$usecase = $usecase[0];
			$page = str_replace(':id:', $usecase['usecaseid'], $page);
			$page = str_replace(':name:', $usecase['name'], $page);
		} else {
			header("Location: viewTracking.php");
		}
		$list = $usecaseDAO->getTracking($id);
		$str = "";
		foreach($list as $requirement) {
			$str .= "<li>".$requirement['requirementid'].' - '.$requirement['description']."</li>";
		}
		if($str != "")
			$page = str_replace(':requirement:', '<ul>'.$str.'</ul>', $page);
		else
			$page = str_replace(':requirement:', "<p>Non ci sono Requisiti associati.</p>", $page);
		echo $page;
	}
?>

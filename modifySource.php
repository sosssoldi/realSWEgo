<?php
	/*error_reporting(E_ALL);
	ini_set('display_errors', 1);*/
	session_start();
	include_once "php/Database/Database.php";
	include_once "php/DAO/DAO.php";
	include_once "php/DAO/SourceDAO.php";
	include_once "php/Object/Object.php";
	include_once "php/Object/Source.php";

	if(empty($_SESSION)) {
		header("Location: index.html");
		exit();
	}

	if(array_key_exists("type", $_SESSION) && $_SESSION["type"] == "admin") {
		header("Location: admin.php");
		exit();
	}

	if(empty($_GET) || !array_key_exists('id', $_GET) || $_GET["id"] == "") {
		header("Location: insertSource.php");
		exit();
	}

	$sourceDAO = new SourceDAO();
	if(!$sourceDAO->getSource($_GET["id"], $_SESSION["id"])) {
		header("Location: insertSource.php");
		exit();
	}

	if(empty($_POST)) {
		render_page($_GET["id"]);
	} else {
		if(!Source::check_input($_POST)) {
			render_page($_GET["id"]);
		} else {
			$_POST = Source::parse_input($_POST);
			$sourceDAO = new SourceDAO();
			$sourceDAO->update($_GET["id"], $_POST);
			header("Location: insertSource.php");
		}
	}
?>

<?php
	function render_page($id = null) {
		$page = file_get_contents("template/modifySource.html");
		$sourceDAO = new SourceDAO();
		$rs = $sourceDAO->getSource($id, $_SESSION["id"]);
		if($rs) {
			$source = $rs[0];
			echo $sourceDAO->fillForm($page, $source);
		} else 
			header("Location: insertSource.php");
	}
?>

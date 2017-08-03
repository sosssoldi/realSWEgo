<?php
	/*error_reporting(E_ALL);
	ini_set('display_errors', 1);*/
	session_start();
	include_once "php/Database/Database.php";
	include_once "php/DAO/DAO.php";
	include_once "php/DAO/SourceDAO.php";
	include_once "php/Object/Object.php";
	include_once "php/Object/Source.php";

	if(empty($_SESSION))
		header("Location: index.html");

	if(empty($_POST)) {
		render_page();
	} else {
		if(!Source::check_input($_POST)) {
			render_page($_POST);
		} else {
			$_POST = Source::parse_input($_POST);
			$sourceDAO = new SourceDAO();
			$sourceDAO->insert($_POST, $_SESSION["id"]);
			header("Location: insertSource.php");
		}
	}
?>

<?php
	function render_page($data = null) {
		$page = file_get_contents("template/insertSource.html");
		$sourceDAO = new SourceDAO();
		echo $sourceDAO->adjustForm($page, $data, $_SESSION["id"]);
	}
?>

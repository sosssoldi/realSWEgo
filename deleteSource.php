<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	session_start();
	include_once "php/Database/Database.php";
	include_once "php/DAO/DAO.php";
	include_once "php/DAO/SourceDAO.php";
	include_once "php/Object/Object.php";
	include_once "php/Object/Source.php";

	/*if(empty($_SESSION))
		header("Location: index.html");*/

	if(empty($_GET) || !array_key_exists('id', $_GET) || $_GET['id'] == "")
		header("Location: home.html");

	if(empty($_POST)) {
		render_page($_GET['id']);
	} else {
		if(array_key_exists('confirm', $_POST)) {
			if($_POST["confirm"] == "si") {
				$sourceDAO = new SourceDAO();
				$sourceDAO->delete($_GET["id"]);
			}
			header("Location: insertSource.php");
		} else
			render_page($_GET['id']);
	}
?>

<?php
	function render_page($id = null) {
		$page = file_get_contents("template/deleteSource.html");
		$sourceDAO = new SourceDAO();
		$source = $sourceDAO->getSource($id);
		if($source) {
			$source = $source[0];
			$page = str_replace(':name:', $source['name'], $page);
		} else {
			header("Location: home.html");
		}
		echo $page;
	}
?>

<?php
	/*error_reporting(E_ALL);
	ini_set('display_errors', 1);*/
	session_start();
	include_once "php/Database/Database.php";
	include_once "php/DAO/DAO.php";
	include_once "php/DAO/ActorDAO.php";
	include_once "php/Object/Object.php";
	include_once "php/Object/Actor.php";

	if(empty($_SESSION))
		header("Location: index.php");

	if(empty($_POST)) {
		render_page();
	} else {
		if(!Actor::check_input($_POST)) {
			render_page($_POST);
		} else {
			$_POST = Actor::parse_input($_POST);
			$actorDAO = new ActorDAO();
			$actorDAO->insert($_POST, $_SESSION["id"]);
			header("Location: insertActor.php");
		}
	}
?>

<?php
	function render_page($data = null) {
		$page = file_get_contents("template/insertActor.html");
		$actorDAO = new ActorDAO();
		echo $actorDAO->adjustForm($page, $data, $_SESSION["id"]);
	}
?>

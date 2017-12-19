<?php
	/*error_reporting(E_ALL);
	ini_set('display_errors', 1);*/
	session_start();
	include_once "php/Database/Database.php";
	include_once "php/DAO/ActorDAO.php";
	include_once "php/Object/Object.php";
	include_once "php/Object/Actor.php";

	if(empty($_SESSION)) {
		header("Location: index.php");
		exit();
	}

	if(array_key_exists("type", $_SESSION) && $_SESSION["type"] == "admin") {
		header("Location: admin.php");
		exit();
	}

	if(empty($_GET) || !array_key_exists('id', $_GET) || $_GET['id'] == "") {
		header("Location: insertActor.php");
		exit();
	}

	$actorDAO = new ActorDAO();
	if(!$actorDAO->getActor($_GET["id"], $_SESSION["id"])) {
		header("Location: insertActor.php");
		exit();
	}

	if(empty($_POST)) {
		render_page($_GET['id']);
	} else {
		if(array_key_exists('confirm', $_POST)) {
			if($_POST["confirm"] == "si") {
				$actorDAO = new ActorDAO();
				$actorDAO->delete($_GET["id"]);
			}
			header("Location: insertActor.php");
		} else
			render_page($_GET['id']);
	}
?>

<?php
	function render_page($id = null) {
		$page = file_get_contents("template/deleteActor.html");
		$actorDAO = new ActorDAO();
		$actor = $actorDAO->getActor($id, $_SESSION["id"]);
		if($actor) {
			$actor = $actor[0];
			$page = str_replace(':name:', $actor['name'], $page);
		} else {
			header("Location: insertActor.php");
		}
		echo $page;
	}
?>

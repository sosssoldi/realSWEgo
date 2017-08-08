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

	if(empty($_GET) || !array_key_exists('id', $_GET) || $_GET["id"] == "")
		header("Location: insertActor.php");

	$actorDAO = new ActorDAO();
	if(!$actorDAO->getActor($_GET["id"], $_SESSION["id"])) {
		header("Location: insertActor.php");
		exit();
	}

	if(empty($_POST)) {
		render_page($_GET["id"]);
	} else {
		if(!Actor::check_input($_POST)) {
			render_page($_GET["id"]);
		} else {
			$_POST = Actor::parse_input($_POST);
			$actorDAO = new ActorDAO();
			$actorDAO->update($_GET["id"], $_POST);
			header("Location: insertActor.php");
		}
	}
?>

<?php
	function render_page($id = null) {
		$page = file_get_contents("template/modifyActor.html");
		$actorDAO = new ActorDAO();
		$rs = $actorDAO->getActor($id, $_SESSION["id"]);
		if($rs) {
			$actor = $rs[0];
			echo $actorDAO->fillForm($page, $actor);
		} else 
			header("Location: insertActor.php");
	}
?>

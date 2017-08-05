<?php
	/*error_reporting(E_ALL);
	ini_set('display_errors', 1);*/
	session_start();
	include_once "php/Database/Database.php";
	include_once "php/DAO/DAO.php";
	include_once "php/DAO/UserDAO.php";
	include_once "php/Object/Object.php";
	include_once "php/Object/User.php";

	if(!empty($_SESSION) && array_key_exists('groupname', $_SESSION) && $_SESSION["groupname"] != "")
		header("Location: viewUsecase.php");

	if(empty($_POST)) {
		render_page();
	} else {
		if(!User::check_login_input($_POST)) {
			render_page($_POST);
		} else {
			$_POST = User::parse_input($_POST);
			$userDAO = new UserDAO();
			$user = $userDAO->login($_POST);
			if($user != null) {
				$_SESSION["id"] = $user["id"];
				$_SESSION["groupname"] = $user["groupname"];
				$_SESSION["projectname"] = $user["projectname"];
				header("Location: viewUsecase.php");
			} else 
				render_page($_POST);
		}
	}
?>

<?php
	function render_page($data = null) {
		$page = file_get_contents("template/login.html");
		$userDAO = new UserDAO();
		echo $userDAO->adjustLoginForm($page, $data);
	}
?>

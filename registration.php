<?php
	/*error_reporting(E_ALL);
	ini_set('display_errors', 1);*/
	session_start();
	include_once "php/Database/Database.php";
	include_once "php/DAO/UserDAO.php";
	include_once "php/Object/Object.php";
	include_once "php/Object/User.php";

	if(array_key_exists("type", $_SESSION) && $_SESSION["type"] == "admin") {
		header("Location: admin.php");
		exit();
	}

	if(!empty($_SESSION) && array_key_exists('groupname', $_SESSION) && $_SESSION["groupname"] != "") {
		header("Location: user.php");
		exit();
	}

	if(empty($_POST)) {
		render_page();
	} else {
		if(!User::check_registration_input($_POST)) {
			render_page($_POST);
		} else {
			$_POST = User::parse_input($_POST);
			$userDAO = new UserDAO();
			$user = $userDAO->insert($_POST);
			if($user != null) {
				$_SESSION["id"] = $user["id"];
				$_SESSION["groupname"] = $user["groupname"];
				$_SESSION["projectname"] = $user["projectname"];
				$_SESSION["type"] = $user["type"];
				header("Location: user.php");
			} else {
				$post = $_POST;
				if($post['password'] == $post['confirmpassword'])
					unset($post['groupname']);
				render_page($post);
			}
		}
	}
?>

<?php
	function render_page($data = null) {
		$page = file_get_contents("template/registration.html");
		$userDAO = new UserDAO();
		echo $userDAO->adjustRegistrationForm($page, $data);
	}
?>

<?php
	/*error_reporting(E_ALL);
	ini_set('display_errors', 1);*/
	session_start();
	include_once "php/Database/Database.php";

	include_once "php/DAO/ActorDAO.php";
	include_once "php/DAO/SourceDAO.php";
	include_once "php/DAO/UsecaseDAO.php";
	include_once "php/DAO/RequirementDAO.php";
	include_once "php/DAO/UserDAO.php";
	include_once "php/Object/Object.php";
	include_once "php/Object/User.php";

	if(empty($_SESSION) || $_SESSION["type"] != 'admin') {
		header("Location: user.php");
		exit();
	}

	if(empty($_POST)) {
		render_page();
	} else {
		if(!User::check_change_password_input($_POST)) {
			render_page();
		} else {
			$_POST = User::parse_input($_POST);
			$userDAO = new UserDAO();
			$userDAO->updatePassword($_POST, $_SESSION["id"]);
			header("Location: admin.php");
		}
	}
?>

<?php
	function render_page($data = null) {
		$page = file_get_contents("template/admin.html");
		$page = str_replace(':groupname:', $_SESSION["groupname"], $page);
		$userDAO = new UserDAO();
		$page = str_replace(':nusers:', $userDAO->countUsers() - 1, $page);
		$page = str_replace(':nactors:', $userDAO->countActors(), $page);
		$page = str_replace(':nsources:', $userDAO->countSources(), $page);
		$page = str_replace(':nusecase:', $userDAO->countUsecase(), $page);
		$page = str_replace(':nrequirements:', $userDAO->countRequirements(), $page);
		$log = $userDAO->getLogs();
		$month = array("Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre");
		$month_data = "";
		for($i = 0; $i < count($log); ++$i)
			if($i == 0)
				$month_data .= "'".$month[$i]."'";
			else
				$month_data .= ",'".$month[$i]."'";
		$page = str_replace(':months:', $month_data, $page);
		$accesses = "";
		for($i = 0; $i < count($log); ++$i)
			if($i == 0)
				$accesses .= $log[$i];
			else
				$accesses .= ",".$log[$i];
		$page = str_replace(':accesses:', $accesses, $page);

		$list = "<ul>";
		for($i = 0; $i < count($log); ++$i)
			$list .= "<li>".$month[$i].": ".$log[$i]."</li>";
		$list .= "</ul>";
		$page = str_replace(':descriptionGraph:', $list, $page);

		echo $page;
	}
?>

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

	if(empty($_SESSION)) {
		header("Location: index.php");
		exit();
	}

	if(array_key_exists("type", $_SESSION) && $_SESSION["type"] == "admin") {
		header("Location: admin.php");
		exit();
	}

	if(empty($_POST)) {
		render_page();
	} else {
		if(!User::check_change_password_input($_POST)) {
			render_page('error_change');
		} else {
			$_POST = User::parse_input($_POST);
			$userDAO = new UserDAO();
			$change = $userDAO->updatePassword($_POST, $_SESSION["id"]);
			if($change)
				render_page('change');
			else
				render_page('error_change');
		}
	}
?>

<?php
	function render_page($data = null) {
		$page = file_get_contents("template/user.html");
		$page = str_replace(':groupname:', $_SESSION["groupname"], $page);
		$actorDAO = new ActorDAO();
		$rs = $actorDAO->select($_SESSION["id"]);
		$page = str_replace(':nactors:', count($rs), $page);
		$sourceDAO = new SourceDAO();
		$rs = $sourceDAO->select($_SESSION["id"]);
		$page = str_replace(':nsources:', count($rs), $page);
		$usecaseDAO = new UsecaseDAO();
		$rs = $usecaseDAO->select($_SESSION["id"]);
		$page = str_replace(':nusecase:', count($rs), $page);
		$requirementDAO = new RequirementDAO();
		$rs = $requirementDAO->select($_SESSION["id"]);
		$reqnumber = count($rs);
		$page = str_replace(':nrequirements:', count($rs), $page);
		$rs = $usecaseDAO->selectTracking($_SESSION["id"]);
		$page = str_replace(':ntrackedusecase:', count($rs), $page);
		$rs = $requirementDAO->selectTrackedRequirements($_SESSION["id"]);
		$page = str_replace(':ntrackedrequirements:', count($rs), $page);
		$rs = $requirementDAO->getRequirementImportanceCount($_SESSION["id"]);
		$page = str_replace(':s0n:', $rs[0], $page);
		$page = str_replace(':u0n:', $rs[1], $page);
		$page = str_replace(':s1n:', $rs[2], $page);
		$page = str_replace(':u1n:', $rs[3], $page);
		$page = str_replace(':s2n:', $rs[4], $page);
		$page = str_replace(':u2n:', $rs[5], $page);
		if($rs[0]+$rs[1] != 0) {
			$page = str_replace(':s0p:', $rs[0]/($rs[0]+$rs[1])*100, $page);
			$page = str_replace(':u0p:', $rs[1]/($rs[0]+$rs[1])*100, $page);
		} else {
			$page = str_replace(':s0p:', '0', $page);
			$page = str_replace(':u0p:', '0', $page);
		}
		if($rs[2]+$rs[3] != 0) {
			$page = str_replace(':s1p:', $rs[2]/($rs[2]+$rs[3])*100, $page);
			$page = str_replace(':u1p:', $rs[3]/($rs[2]+$rs[3])*100, $page);
		} else {
			$page = str_replace(':s1p:', '0', $page);
			$page = str_replace(':u1p:', '0', $page);
		}
		if($rs[4]+$rs[5] != 0) {
			$page = str_replace(':s2p:', $rs[4]/($rs[4]+$rs[5])*100, $page);
			$page = str_replace(':u2p:', $rs[5]/($rs[4]+$rs[5])*100, $page);
		} else  {
			$page = str_replace(':s2p:', '0', $page);
			$page = str_replace(':u2p:', '0', $page);
		}
		if($data == 'change')
			$page = str_replace(':message:', '<p class="message success">Password cambiata!</p>', $page);
		else if($data == 'error_change')
			$page = str_replace(':message:', '<p class="message warning">La password non &egrave; stata cambiata. Inserire correttamente i dati nel form.</p>', $page);
		else
			$page = str_replace(':message:', '', $page);
		echo $page;
	}
?>

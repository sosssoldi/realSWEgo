<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	session_start();
	include_once "php/Database/Database.php";
	include_once "php/DAO/DAO.php";
	include_once "php/DAO/RequirementDAO.php";
	include_once "php/Object/Object.php";
	include_once "php/Object/Requirement.php";

	/*if(empty($_SESSION))
		header("Location: index.html");*/

	if(empty($_POST)) {
		render_page();
	} else {
		if(!Requirement::check_input($_POST)) {
			render_page($_POST);
		} else {
			$_POST = Requirement::parse_input($_POST);
			$requirementDAO = new RequirementDAO();
			$requirementDAO->insert($_POST);
			header("Location: insertRequirement.php");
		}
	}
?>

<?php
	function render_page($data = null) {
		$page = file_get_contents("template/insertRequirement.html");
		$requirementDAO = new RequirementDAO();
		echo $requirementDAO->adjustForm($page, $data);
	}
?>

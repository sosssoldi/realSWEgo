<?php
	/*error_reporting(E_ALL);
	ini_set('display_errors', 1);*/
	session_start();
	include_once "php/Database/Database.php";
	include_once "php/DAO/RequirementDAO.php";
	include_once "php/Object/Object.php";
	include_once "php/Object/Requirement.php";

	if(empty($_SESSION)) {
		header("Location: index.php");
		exit();
	}

	if(array_key_exists("type", $_SESSION) && $_SESSION["type"] == "admin") {
		header("Location: admin.php");
		exit();
	}

	if(empty($_GET) || !array_key_exists('id', $_GET) || $_GET["id"] == "") {
		header("Location: viewRequirement.php");
		exit();
	}

	$requirementDAO = new RequirementDAO();
	if(!$requirementDAO->getRequirement($_GET["id"], $_SESSION["id"])) {
		header("Location: viewRequirement.php");
		exit();
	}

	if(empty($_POST)) {
		render_page($_GET["id"]);
	} else {
		if(!Requirement::check_input($_POST)) {
			render_page($_GET["id"]);
		} else {
			$_POST = Requirement::parse_input($_POST);
			$requirementDAO = new RequirementDAO();
			$requirementDAO->update($_GET["id"], $_POST, $_SESSION["id"]);
			header("Location: viewRequirement.php");
		}
	}
?>

<?php
	function render_page($id = null) {
		$page = file_get_contents("template/modifyRequirement.html");
		$requirementDAO = new RequirementDAO();
		$rs = $requirementDAO->getRequirement($id, $_SESSION["id"]);
		if($rs) {
			$requirement = $rs[0];
			echo $requirementDAO->fillForm($page, $requirement, $_SESSION["id"]);
		} else
			header("Location: viewRequirement.php");
	}
?>

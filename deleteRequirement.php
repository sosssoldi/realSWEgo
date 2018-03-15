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

	if(empty($_GET) || !array_key_exists('id', $_GET) || $_GET['id'] == "") {
		header("Location: viewRequirement.php");
		exit();
	}

	$requirementDAO = new RequirementDAO();
	if(!$requirementDAO->getRequirement($_GET["id"], $_SESSION["id"])) {
		header("Location: viewRequirement.php");
		exit();
	}

	if(empty($_POST)) {
		render_page($_GET['id']);
	} else {
		if(array_key_exists('confirm', $_POST)) {
			if($_POST["confirm"] == "si") {
				$requirementDAO = new RequirementDAO();
				$requirementDAO->delete($_GET["id"]);
			}
			header("Location: viewRequirement.php");
		} else
			render_page($_GET['id']);
	}
?>

<?php
	function render_page($id = null) {
		$page = file_get_contents("template/deleteRequirement.html");
		$requirementDAO = new RequirementDAO();
		$requirement = $requirementDAO->getRequirement($id, $_SESSION["id"]);
		if($requirement) {
			$requirement = $requirement[0];
			$page = str_replace(':id:', $requirement['requirementid'], $page);
			$page = str_replace(':name:', $requirement['name'], $page);
		} else {
			header("Location: viewRequirement.php");
		}
		$list = $requirementDAO->getHierarchy($id);
		$str = "<ul>";
		foreach($list as $requirement) {
			$str .= "<li>".$requirement['requirementid'].' - '.$requirement['name']."</li>";
		}
		$str .= "</ul>";
		if($str != "")
			$page = str_replace(':requirement:', $str, $page);
		else
			$page = str_replace(':requirement:', "<p>Nessun'altro requisito verr&agrave; eliminato</p>", $page);
		echo $page;
	}
?>

<?php
	/*error_reporting(E_ALL);
	ini_set('display_errors', 1);*/
	session_start();
	include_once "php/Database/Database.php";
	include_once "php/DAO/DAO.php";
	include_once "php/DAO/UsecaseDAO.php";
	include_once "php/Object/Object.php";
	include_once "php/Object/Usecase.php";

	if(empty($_SESSION))
		header("Location: index.php");

	if(empty($_GET) || !array_key_exists('id', $_GET) || $_GET['id'] == "")
		header("Location: viewUsecase.php");

	$usecaseDAO = new UsecaseDAO();
	if(!$usecaseDAO->getUsecase($_GET["id"], $_SESSION["id"])) {
		header("Location: viewUsecase.php");
		exit();
	}

	if(empty($_POST)) {
		render_page($_GET['id']);
	} else {
		if(array_key_exists('confirm', $_POST)) {
			if($_POST["confirm"] == "si") {
				$usecaseDAO = new UsecaseDAO();
				$usecaseDAO->delete($_GET["id"]);
			}
			header("Location: viewUsecase.php");
		} else
			render_page($_GET['id']);
	}
?>

<?php
	function render_page($id = null) {
		$page = file_get_contents("template/deleteUsecase.html");
		$usecaseDAO = new UsecaseDAO();
		$usecase = $usecaseDAO->getUsecase($id, $_SESSION["id"]);
		if($usecase) {
			$usecase = $usecase[0];
			$page = str_replace(':id:', $usecase['usecaseid'], $page);
			$page = str_replace(':name:', $usecase['name'], $page);
		} else {
			header("Location: viewUsecase.php");
		}
		$list = $usecaseDAO->getHierarchy($id);
		$str = "";
		foreach($list as $uc) {
			$str .= "<li>".$uc['usecaseid'].' - '.$uc['name']."</li>";
		}
		$list = $usecaseDAO->getInclusions($id);
		foreach($list as $inclusion) {
			$uc1 = $usecaseDAO->getUsecase($inclusion['usecaseid'], $_SESSION["id"]);
			$uc1 = $uc1[0];
			$uc2 = $usecaseDAO->getUsecase($inclusion['includedusecaseid'], $_SESSION["id"]);
			$uc2 = $uc2[0];
			$str .= "<li>Inclusione tra ".$uc1['usecaseid'].' e '.$uc2['usecaseid']."</li>";
		}
		$list = $usecaseDAO->getExtensions($id);
		foreach($list as $extension) {
			$uc1 = $usecaseDAO->getUsecase($extension['usecaseid'], $_SESSION["id"]);
			$uc1 = $uc1[0];
			$uc2 = $usecaseDAO->getUsecase($extension['extendedusecaseid'], $_SESSION["id"]);
			$uc2 = $uc2[0];
			$str .= "<li>Estensione tra ".$uc1['usecaseid'].' e '.$uc2['usecaseid']."</li>";
		}
		if($str != "")
			$page = str_replace(':usecase:', '<ul>'.$str.'</ul>', $page);
		else
			$page = str_replace(':usecase:', "<p>Nessun'altro <span lang=\"en\">Use Case</span> verr&agrave; rimosso.</p>", $page);
		echo $page;
	}
?>

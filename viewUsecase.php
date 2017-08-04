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
		header("Location: index.html");

	$usecaseDAO = new UsecaseDAO();
	$rs = $usecaseDAO->select($_SESSION["id"]);
	if($rs) {
		$str = '<table id="allUC">';
		$str .= '<thead>';
		$str .= '<tr>';
		$str .= '<th scope="col">Codice</th>';
		$str .= '<th scope="col">Nome</th>';
		$str .= '<th scope="col">Descrizione</th>';
		$str .= '<th scope="col">Precondizione</th>';
		$str .= '<th scope="col">Postcondizione</th>';
		$str .= '<th scope="col">ScenarioPrincipale</th>';
		$str .= '</tr>';
		$str .= '</thead>';
		$str .= '<tbody>';
		foreach($rs as $uc) {
			$html = file_get_contents("template/UsecaseTableRow.html");
			$html = str_replace(':usecaseid:', $uc['usecaseid'], $html);
			$html = str_replace(':name:', $uc['name'], $html);
			$html = str_replace(':description:', $uc['description'], $html);
			$html = str_replace(':precondition:', $uc['precondition'], $html);
			$html = str_replace(':postcondition:', $uc['postcondition'], $html);
			$html = str_replace(':mainscenario:', $uc['mainscenario'], $html);
			$html = str_replace(':id:', $uc['id'], $html);
			$str .= $html;
		}
		$str .= '</tbody>';
		$str .= '</table>';
	} else {
		$str = '<p id="noResult">Non ci sono Use Case.</p>';
	}
	render_page($str);
?>

<?php
	function render_page($usecase) {
		$page = file_get_contents("template/viewUsecase.html");
		echo str_replace(':usecase:', $usecase, $page);
	}
?>

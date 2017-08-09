<?php
	/*error_reporting(E_ALL);
	ini_set('display_errors', 1);*/
	session_start();
	include_once "php/Database/Database.php";
	include_once "php/DAO/DAO.php";
	include_once "php/DAO/RequirementDAO.php";
	include_once "php/Object/Object.php";
	include_once "php/Object/Requirement.php";

	if(empty($_SESSION))
		header("Location: index.php");

	$requirementDAO = new RequirementDAO();
	$rs = $requirementDAO->select($_SESSION["id"]);
	if($rs) {
		$str = '<table id="allR">';
		$str .= '<thead>';
		$str .= '<tr>';
		$str .= '<th scope="col">Codice</th>';
		$str .= '<th scope="col">Descrizione</th>';
		$str .= '<th scope="col">Tipo</th>';
		$str .= '<th scope="col">Importanza</th>';
		$str .= '<th scope="col">Stato d\'implementazione</th>';
		$str .= '<th scope="col">Fonti</th>';
		$str .= '<th scope="col">Operazioni</th>';
		$str .= '</tr>';
		$str .= '</thead>';
		$str .= '<tbody>';
		foreach($rs as $requirement) {
			$html = file_get_contents("template/RequirementTableRow.html");
			$html = str_replace(':requirementid:', $requirement['requirementid'], $html);
			$html = str_replace(':description:', $requirement['description'], $html);
			$html = str_replace(':type:', $requirement['type'], $html);
			$html = str_replace(':importance:', $requirement['importance'], $html);
			$html = str_replace(':satisfied:', $requirement['satisfied'], $html);
			$html = str_replace(':id:', $requirement['id'], $html);

			$sources = $requirementDAO->selectSources($_SESSION['id']);
			$htmlSource = '';
			foreach($sources as $source) {
				if($source['id'] == $requirement['source'])
					$htmlSource = $source['name'];
			}
			if($htmlSource != '')
				$html = str_replace(':sources:', $htmlSource, $html);
			else
				$html = str_replace(':sources:', 'Nessuna fonte', $html);
	
			$str .= $html;
		}
		$str .= '</tbody>';
		$str .= '</table>';
	} else {
		$str = '<p id="noResult">Non ci sono requisiti.</p>';
	}
	render_page($str);
?>

<?php
	function render_page($requirement) {
		$page = file_get_contents("template/viewRequirement.html");
		echo str_replace(':requirement:', $requirement, $page);
	}
?>

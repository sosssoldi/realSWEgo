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
	$rs = $usecaseDAO->selectTracking($_SESSION["id"]);
	if(!empty($rs)) {
		$str = '<table>';
		$str .= '<thead>';
		$str .= '<tr>';
		$str .= '<th scope="col">Codice Use Case</th>';
		$str .= '<th scope="col">Codice Requisiti</th>';;
		$str .= '</tr>';
		$str .= '</thead>';
		$str .= '<tbody>';
		foreach($rs as $ucid => $rid) {
			$html = '<tr>';
			$html .= '<td>'.$ucid.'</td>';
			$html .= '<td>'.$rid.'</td>';
			$html .= '</tr>';
			$str .= $html;
		}
		$str .= '</tbody>';
		$str .= '</table>';
	} else {
		$str = '<p id="noResult">Non ci sono Use Case tracciati con i Requisiti.</p>';
	}
	render_page($str);
?>

<?php
	function render_page($track) {
		$page = file_get_contents("template/viewTracking.html");
		echo str_replace(':tracking:', $track, $page);
	}
?>

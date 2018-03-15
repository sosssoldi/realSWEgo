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

	$requirementDAO = new RequirementDAO();
	$rs = $requirementDAO->select($_SESSION["id"]);
	if($rs) {
		$rs = sortRequirements($rs);
		$str = '<table id="allR">';
		$str .= '<caption>Tabella Requisiti</caption>';
		$str .= '<thead>';
		$str .= '<tr>';
		$str .= '<th scope="col">Codice</th>';
		$str .= '<th scope="col">Nome</th>';
		$str .= '<th scope="col">Descrizione</th>';
		$str .= '<th scope="col">Tipo</th>';
		$str .= '<th scope="col">Importanza</th>';
		$str .= '<th scope="col">Stato d\'implementazione</th>';
		$str .= '<th scope="col">Fonte</th>';
		$str .= '<th scope="col">Operazioni</th>';
		$str .= '</tr>';
		$str .= '</thead>';
		$str .= '<tbody>';
		foreach($rs as $requirement) {
			$html = file_get_contents("template/RequirementTableRow.html");
			$html = str_replace(':requirementid:', $requirement['requirementid'], $html);
			$html = str_replace(':name:', $requirement['name'], $html);
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

	function sortRequirements($requirement) {
		$array = array("F" => array(), "Q" => array(), "V" => array(), "P" => array());
		foreach($requirement as $r) {
			switch(substr($r["requirementid"], 2, 1)) {
				case "F":
					array_push($array["F"], $r/*array($r["id"], $r["requirementid"], $r["parent"])*/);
					break;
				case "Q":
					array_push($array["Q"], $r/*array($r["id"], $r["requirementid"], $r["parent"])*/);
					break;
				case "V":
					array_push($array["V"], $r/*array($r["id"], $r["requirementid"], $r["parent"])*/);
					break;
				case "P":
					array_push($array["P"], $r/*array($r["id"], $r["requirementid"], $r["parent"])*/);
					break;
			}
	    }

		foreach($array as &$specificArray)
	    	for($j = 0; $j < count($specificArray); ++$j)
	        	for($i = 0; $i < count($specificArray); ++$i)
					if(comparator($specificArray[$j]["requirementid"], $specificArray[$i]["requirementid"]) == 1) {
		                $temp = $specificArray[$j];
		                $specificArray[$j] = $specificArray[$i];
		                $specificArray[$i] = $temp;
		            }
		return array_merge($array["F"], $array["Q"], $array["V"], $array["P"]);
	}

	function comparator($id1, $id2) {
        if($id1 == $id2)
            return 0;
        $id1 = substr($id1, 3);
        $id2 = substr($id2, 3);
        $pieces1 = explode(".", $id1);
        $pieces2 = explode(".", $id2);

        for($i = 0; $i < min(count($pieces1), count($pieces2)); ++$i) {
            if($pieces1[$i] != $pieces2[$i])
                if($pieces1[$i] < $pieces2[$i])
                    return 1;
                else
                    return -1;
        }

		if(count($pieces1) < count($pieces2))
			return 1;
		else
			return -1;
    }
?>

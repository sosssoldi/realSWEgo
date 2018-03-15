 <?php
	/*error_reporting(E_ALL);
	ini_set('display_errors', 1);*/
	session_start();
	include_once "php/Database/Database.php";
	include_once "php/DAO/UsecaseDAO.php";
	include_once "php/DAO/ActorDAO.php";
	include_once "php/Object/Object.php";
	include_once "php/Object/Usecase.php";

	if(empty($_SESSION)) {
		header("Location: index.php");
		exit();
	}

	if(array_key_exists("type", $_SESSION) && $_SESSION["type"] == "admin") {
		header("Location: admin.php");
		exit();
	}

	$usecaseDAO = new UsecaseDAO();
	$actorDAO = new ActorDAO();
	$rs = $usecaseDAO->select($_SESSION["id"]);
	if($rs) {
        $rs = sortUsecase($rs);
		$str = '<table id="allUC">';
		$str .= '<caption>Tabella <span lang="en">Use Case</span></caption>';
		$str .= '<thead>';
		$str .= '<tr>';
		$str .= '<th scope="col">Codice</th>';
		$str .= '<th scope="col">Nome</th>';
		$str .= '<th scope="col">Descrizione</th>';
		$str .= '<th scope="col">Precondizione</th>';
		$str .= '<th scope="col">Postcondizione</th>';
		$str .= '<th scope="col">ScenarioPrincipale</th>';
		$str .= '<th scope="col">Inclusioni</th>';
		$str .= '<th scope="col">Estensioni</th>';
		$str .= '<th scope="col">Attori</th>';
		$str .= '<th scope="col">Operazioni</th>';
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

			$inclusions = $usecaseDAO->getMyInclusions($uc['id']);
			$htmlInclusions = '';
			foreach($inclusions as $inclusion) {
				$in = $usecaseDAO->getUsecase($inclusion['includedusecaseid'], $_SESSION["id"]);
				if($in) {
					$htmlInclusions .= $in[0]['usecaseid'];
					$htmlInclusions .= ', ';
				}
			}
			if($htmlInclusions == '')
				$html = str_replace(':inclusions:', 'Nessuna inclusione', $html);
			else {
				$htmlInclusions = rtrim($htmlInclusions, ', ');
				$html = str_replace(':inclusions:', $htmlInclusions, $html);
			}

			$extensions = $usecaseDAO->getMyExtensions($uc['id']);
			$htmlExtensions = '';
			foreach($extensions as $extension) {
				$ex = $usecaseDAO->getUsecase($extension['extendedusecaseid'], $_SESSION["id"]);
				if($ex) {
					$htmlExtensions .= $ex[0]['usecaseid'];
					$htmlExtensions .= ', ';
				}
			}
			if($htmlExtensions == '')
				$html = str_replace(':extensions:', 'Nessuna estensione', $html);
			else {
				$htmlExtensions = rtrim($htmlExtensions, ', ');
				$html = str_replace(':extensions:', $htmlExtensions, $html);
			}

			$actors = $usecaseDAO->getActors($uc['id']);
			$htmlActor = '';
			foreach($actors as $actor) {
				$act = $actorDAO->getActor($actor['actorsid'], $_SESSION['id']);
				if($act) {
					$htmlActor .= $act[0]['name'];
					$htmlActor .= ', ';
				}
			}
			if($htmlActor == '')
				$html = str_replace(':actors:', 'Nessun attore', $html);
			else {
				$htmlActor = rtrim($htmlActor, ', ');
				$html = str_replace(':actors:', $htmlActor, $html);
			}

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

    function sortUsecase($usecase) {
        $array = array();
        foreach($usecase as $uc) {
            array_push($array, $uc);
        }

        for($j = 0; $j < count($array); ++$j)
            for($i = 0; $i < count($array); ++$i)
    			if(comparator($array[$j]["usecaseid"], $array[$i]["usecaseid"]) == 1) {
                    $temp = $array[$j];
                    $array[$j] = $array[$i];
                    $array[$i] = $temp;
                }
        return $array;
    }

    function comparator($id1, $id2) {
        if($id1 == $id2)
            return 0;
        $id1 = str_replace("UC", "", $id1);
        $id2 = str_replace("UC", "", $id2);
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

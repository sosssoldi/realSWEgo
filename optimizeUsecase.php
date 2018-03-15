<?php
	/*error_reporting(E_ALL);
	ini_set('display_errors', 1);*/
	session_start();
	include_once "php/Database/Database.php";
	include_once "php/DAO/UsecaseDAO.php";
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
    $usecase = $usecaseDAO->select($_SESSION["id"]);

    $array = array();
    foreach($usecase as $uc) {
        array_push($array, array($uc["id"], $uc["usecaseid"], $uc["parent"]));
    }

    for($j = 0; $j < count($array); ++$j)
        for($i = 0; $i < count($array); ++$i)
			if($i != $j)
				if(comparator($array[$j][1], $array[$i][1]) == 1) {
	                $temp = $array[$j];
	                $array[$j] = $array[$i];
	                $array[$i] = $temp;
	            }

    //print_r($array);

	$points = 0;
	for($i = 0; $i < count($array); ++$i) {
		if(count(explode(".", $array[$i][1])) - 1 > $points)
			$points = count(explode(".", $array[$i][1])) - 1;
	}

	$matrix = array();
	for($i = 0; $i < count($array); ++$i)
		for($j = 0; $j < $points + 1; ++$j)
			$matrix[$i][$j] = "";

	//print_r($matrix);

	for($i = 0; $i < count($array); ++$i) {
		$temp = explode(".", str_replace("UC", "", $array[$i][1]));
		for($j = 0; $j < count($temp); ++$j)
			$matrix[$i][$j] = $temp[$j];
	}

	//print_r($matrix);

	for($j = 0; $j < $points + 1; ++$j) {
		$expected = 1;
		$value = $matrix[0][$j];
		//echo "[e:$expected,v:$value] | ";
		for($i = 0; $i < count($array); ++$i) {
			if($matrix[$i][$j] == "") {
				$expected = 0;
				$value = -1;
				//echo "empty -> expected = 0 | ";
			} else {
				if($matrix[$i][$j] == $value) {
					$matrix[$i][$j] = $expected;
					//echo "== value($value) -> m[$i][$j] = $expected | ";
				} else {
					//echo "!= value($value) -> ";
					$value = $matrix[$i][$j];
					++$expected;
					$matrix[$i][$j] = $expected;
					//echo "m[$i][$j] = $expected | ";
				}
			}
		}
	}

	//print_r($matrix);

	$database = new Database();
	for($i = 0; $i < count($array); ++$i) {
		$temp = implode(".", $matrix[$i]);
		$temp = rtrim($temp, ".");
		$query = "UPDATE usecase SET usecaseid = 'UC{$temp}' WHERE id = {$array[$i][0]};<br>";
		//echo $query."<br>";
		$database->query($query);
		$database->execute();
	}

	header("Location: viewUsecase.php");

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

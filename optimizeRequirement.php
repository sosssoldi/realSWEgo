<?php
	/*error_reporting(E_ALL);
	ini_set('display_errors', 1);*/
	session_start();
	include_once "php/Database/Database.php";
	include_once "php/DAO/RequirementDAO.php";
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

	$requirementDAO = new RequirementDAO();
    $requirement = $requirementDAO->select($_SESSION["id"]);

    $array = array("F" => array(), "Q" => array(), "V" => array(), "P" => array());
    foreach($requirement as $r) {
		switch(substr($r["requirementid"], 2, 1)) {
			case "F":
	        	array_push($array["F"], array($r["id"], $r["requirementid"], $r["parent"]));
				break;
			case "Q":
				array_push($array["Q"], array($r["id"], $r["requirementid"], $r["parent"]));
				break;
			case "V":
				array_push($array["V"], array($r["id"], $r["requirementid"], $r["parent"]));
				break;
			case "P":
				array_push($array["P"], array($r["id"], $r["requirementid"], $r["parent"]));
				break;
		}
    }

	foreach($array as &$specificArray)
    	for($j = 0; $j < count($specificArray); ++$j)
        	for($i = 0; $i < count($specificArray); ++$i)
				if(comparator($specificArray[$j][1], $specificArray[$i][1]) == 1) {
	                $temp = $specificArray[$j];
	                $specificArray[$j] = $specificArray[$i];
	                $specificArray[$i] = $temp;
	            }

    //print_r($array);

	$points = array("F" => 0, "Q" => 0, "V" => 0, "P" => 0);
	foreach($array as $key => &$specificArray)
		for($i = 0; $i < count($specificArray); ++$i) {
			if(count(explode(".", $specificArray[$i][1])) - 1 > $points[$key])
				$points[$key] = count(explode(".", $specificArray[$i][1])) - 1;
		}

	$matrix = array("F" => array(), "Q" => array(), "V" => array(), "P" => array());
	foreach($matrix as $key => &$specificArray)
		for($i = 0; $i < count($array[$key]); ++$i)
			for($j = 0; $j < $points[$key] + 1; ++$j)
				$specificArray[$i][$j] = "";

	//print_r($matrix);

	foreach($matrix as $key => &$specificArray)
		for($i = 0; $i < count($array[$key]); ++$i) {
			$temp = explode(".", substr($array[$key][$i][1], 3));
			for($j = 0; $j < count($temp); ++$j)
				$specificArray[$i][$j] = $temp[$j];
		}

	//print_r($matrix);

	foreach($matrix as $key => &$specificArray)
		for($j = 0; $j < $points[$key] + 1; ++$j) {
			$expected = 1;
			$value = $specificArray[0][$j];
			//echo "[e:$expected,v:$value] | ";
			for($i = 0; $i < count($array[$key]); ++$i) {
				if($specificArray[$i][$j] == "") {
					$expected = 0;
					$value = -1;
					//echo "empty -> expected = 0 | ";
				} else {
					if($specificArray[$i][$j] == $value) {
						$specificArray[$i][$j] = $expected;
						//echo "== value($value) -> m[$i][$j] = $expected | ";
					} else {
						//echo "!= value($value) -> ";
						$value = $specificArray[$i][$j];
						++$expected;
						$specificArray[$i][$j] = $expected;
						//echo "m[$i][$j] = $expected | ";
					}
				}
			}
		}

	//print_r($matrix);

	$database = new Database();
	foreach($matrix as $key => &$specificArray)
		for($i = 0; $i < count($array[$key]); ++$i) {
			$temp = implode(".", $specificArray[$i]);
			$temp = rtrim($temp, ".");
			$prefix = substr($array[$key][$i][1], 0, 3);
			$query = "UPDATE requirements SET requirementid = '{$prefix}{$temp}' WHERE id = {$array[$key][$i][0]};<br>";
			//echo $query."<br>";
			$database->query($query);
			$database->execute();
		}

	header("Location: viewRequirement.php");

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

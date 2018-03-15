<?php
	include_once "Object.php";

	class Usecase extends Object {
		//Metodo che controlla se le informazioni inserite riguardo allo usecase sono complete
		public static function check_input($obj) {
			if(!array_key_exists('name', $obj) || $obj["name"] == "")
				return false;
			if(!array_key_exists('description', $obj) || $obj["description"] == "")
				return false;
			if(!array_key_exists('precondition', $obj) || $obj["precondition"] == "")
				return false;
			if(!array_key_exists('postcondition', $obj) || $obj["postcondition"] == "")
				return false;
			if(!array_key_exists('mainscenario', $obj) || $obj["mainscenario"] == "")
				return false;
			if(!array_key_exists('generalization', $obj) || $obj["generalization"] == "")
				return false;
			if(!array_key_exists('inclusion', $obj) || $obj["inclusion"] == "")
				return false;
			if(!array_key_exists('extension', $obj) || $obj["extension"] == "")
				return false;
			/*if(!array_key_exists('actor', $obj) || $obj["actor"] == "")
				return false;*/
			if(!array_key_exists('parent', $obj))
				return false;
			return true;
		}
		//Metodo che controlla se le informazioni inserite riguardo allo usecase e ai requisiti nel form del tracciamento sono complete
		public static function check_tracking_input($obj) {
			if(!array_key_exists('usecase', $obj) || $obj["usecase"] == "")
				return false;
			if(!array_key_exists('requirement', $obj) || $obj["requirement"] == "")
				return false;
			return true;
		}
		//Metodo che fa il parsing delle informazioni inserite all'interno del form
		public static function parse_input($obj) {
			foreach($obj as $key => &$value) {
                                if($key != "generalization" && $key != "inclusion" && $key != "extension" && $key != "actor" && $key != "requirement")
					$value = htmlentities($value, ENT_QUOTES);
				/*$value = str_replace("'", "'", $value);
				$value = str_replace("<", "<", $value);
				$value = str_replace(">", ">", $value);*/
			}
			return $obj;
		}
	}
?>

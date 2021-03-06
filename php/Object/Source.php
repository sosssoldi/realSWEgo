<?php
	include_once "Object.php";

	class Source extends Object {
		//Metodo che controlla se le informazioni inserite riguardo alla fonte sono complete
		public static function check_input($obj) {
			if(!array_key_exists('name', $obj) || $obj["name"] == "")
				return false;
			if(!array_key_exists('description', $obj) || $obj["description"] == "")
				return false;
			return true;
		}
		//Metodo che fa il parsing delle informazioni inserite all'interno del form
		public static function parse_input($obj) {
			foreach($obj as $key => &$value) {
				$value = htmlentities($value, ENT_QUOTES);
				/*$value = str_replace("'", "'", $value);
				$value = str_replace("<", "<", $value);
				$value = str_replace(">", ">", $value);*/
			}
			return $obj;
		}
	}
?>

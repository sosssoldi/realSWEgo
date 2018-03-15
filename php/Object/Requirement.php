<?php
	include_once "Object.php";

	class Requirement extends Object {
		//Metodo che controlla se le informazioni inserite riguardo al requisito sono complete
		public static function check_input($obj) {
			if(!array_key_exists('name', $obj) || $obj["name"] == "")
				return false;
			if(!array_key_exists('description', $obj) || $obj["description"] == "")
				return false;
			if(!array_key_exists('type', $obj) || $obj["type"] == "")
				return false;
			if(!array_key_exists('importance', $obj) || $obj["importance"] == "")
				return false;
			if(!array_key_exists('satisfied', $obj) || $obj["satisfied"] == "")
				return false;
			if(!array_key_exists('source', $obj) || $obj["source"] == "")
				return false;
			if(!array_key_exists('parent', $obj) || $obj["parent"] == "")
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

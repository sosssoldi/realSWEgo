<?php
	include_once "Object.php";

	class Usecase extends Object {
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
			if(!array_key_exists('parent', $obj))
				return false;
			return true;
		}

		public static function check_tracking_input($obj) {
			if(!array_key_exists('usecase', $obj) || $obj["usecase"] == "")
				return false;
			if(!array_key_exists('requirement', $obj) || $obj["requirement"] == "")
				return false;
			return true;
		}

		public static function parse_input($obj) {
			foreach($obj as $key => &$value)
				$value = str_replace("'", "\'", $value);
			return $obj;
		}
	}
?>

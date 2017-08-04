<?php
	include_once "Object.php";

	class Actor extends Object {
		public static function check_input($obj) {
			if(!array_key_exists('name', $obj) || $obj["name"] == "")
				return false;
			if(!array_key_exists('description', $obj) || $obj["description"] == "")
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

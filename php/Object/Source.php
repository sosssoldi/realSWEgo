<?php
	include_once "Object.php";

	class Source extends Object {
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
				$value = str_replace("<", "&lt;", $value);
				$value = str_replace(">", "&gt;", $value);
			return $obj;
		}
	}
?>

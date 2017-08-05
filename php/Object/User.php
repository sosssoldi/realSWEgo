<?php
	include_once "Object.php";

	class User extends Object {
		public static function check_login_input($obj) {
			if(!array_key_exists('groupname', $obj) || $obj["groupname"] == "")
				return false;
			if(!array_key_exists('password', $obj) || $obj["password"] == "")
				return false;
			return true;
		}

		public static function check_registration_input($obj) {
			if(!array_key_exists('groupname', $obj) || $obj["groupname"] == "")
				return false;
			if(!array_key_exists('projectname', $obj) || $obj["projectname"] == "")
				return false;
			if(!array_key_exists('password', $obj) || $obj["password"] == "")
				return false;
			if(!array_key_exists('confirmpassword', $obj) || $obj["confirmpassword"] == "")
				return false;
			if(array_key_exists('password', $obj) && array_key_exists('confirmpassword', $obj) && $obj["password"] != $obj["confirmpassword"])
				return false;
			return true;
		}

		public static function check_change_password_input($obj) {
			if(!array_key_exists('oldpassword', $obj) || $obj["oldpassword"] == "")
				return false;
			if(!array_key_exists('password', $obj) || $obj["password"] == "")
				return false;
			if(!array_key_exists('confirmpassword', $obj) || $obj["confirmpassword"] == "")
				return false;
			if(array_key_exists('password', $obj) && array_key_exists('confirmpassword', $obj) && $obj["password"] != $obj["confirmpassword"])
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

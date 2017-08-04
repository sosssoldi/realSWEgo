<?php
	class UserDAO extends Database {
		
		public function __construct($connection = null) {
			parent::__construct($connection);
		}

		public function insert($obj) {
			$this->query("SELECT * FROM users WHERE groupname = '{$obj["groupname"]}';");
			$rs = $this->resultSet();
			if(!$rs) {
				$pwd = hash('sha256', $obj["password"]."let's salt");
				$this->query("INSERT INTO users VALUES (id, '{$obj["groupname"]}', '{$obj["projectname"]}', '{$pwd}');");
				$this->resultSet();
				$rs = $this->select();
				if($rs) {
					$found = null;
					foreach($rs as $user) {
						if($user["groupname"] == $obj["groupname"])
							$found = $user;
					}
				}
				return $found;
			}
			return null;
		}

		public function login($obj) {
			$rs = $this->select();
			if($rs) {
				$found = null;
				foreach($rs as $user) {
					if($obj["groupname"] == $user["groupname"] && hash("sha256", $obj["password"]."let's salt") == $user["password"])
						$found = $user;
				}
				return $found;
			}
			return null;
		}

		public function select() {
			$this->query('SELECT * FROM users ORDER BY groupname;');
			return $this->resultSet();
		}

		public function adjustLoginForm($page, $data) {
			if($data) {
				if(array_key_exists('groupname', $data))
					$page = str_replace(':groupname:', $data['groupname'], $page);
			} else {
				$page = str_replace(':groupname:', '', $page);
			}
			return $page;
		}

		public function adjustRegistrationForm($page, $data) {
			if($data) {
				if(array_key_exists('groupname', $data))
					$page = str_replace(':groupname:', $data['groupname'], $page);
				if(array_key_exists('projectname', $data))
					$page = str_replace(':projectname:', $data['projectname'], $page);
			} else {
				$page = str_replace(':groupname:', '', $page);
				$page = str_replace(':projectname:', '', $page);
			}
			return $page;
		}
	}
?>

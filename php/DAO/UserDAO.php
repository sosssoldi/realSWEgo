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
				$this->query("INSERT INTO users VALUES (id, '{$obj["groupname"]}', '{$obj["projectname"]}', '{$pwd}', 'user');");
				$this->resultSet();
				$rs = $this->select();
				if($rs) {
					$found = null;
					foreach($rs as $user) {
						if($user["groupname"] == $obj["groupname"]) {
							$found = $user;
							$this->saveAccess($user);
						}
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
					if($obj["groupname"] == $user["groupname"] && hash("sha256", $obj["password"]."let's salt") == $user["password"]) {
						$found = $user;
						$this->saveAccess($user);
					}
				}
				return $found;
			}
			return null;
		}

		public function saveAccess($user) {
			$this->query("INSERT INTO login VALUES({$user["id"]},NOW());");
			return $this->resultSet();
		}

		public function updatePassword($obj, $userid) {
			$this->query("SELECT password FROM users WHERE id = {$userid};");
			$rs = $this->resultSet();
			if($rs) {
				$rs = $rs[0];
				$pwd = $rs['password'];
				if(hash('sha256', $obj["oldpassword"]."let's salt") == $pwd) {
					$newpwd = hash('sha256', $obj["password"]."let's salt");
					$this->query("UPDATE users SET password = '{$newpwd}' WHERE id = {$userid};");
					$this->resultSet();
					return true;
				}
			} else
				return false;
		}

		public function select() {
			$this->query('SELECT * FROM users ORDER BY groupname;');
			return $this->resultSet();
		}

		public function getLogs() {
			$year = Date("Y");
			$month = Date("m");
			$this->query("SELECT MONTH(date_time) as month, count(*) as naccess FROM login WHERE YEAR(date_time) = {$year} GROUP BY MONTH(date_time) ORDER BY month DESC;");
			$rs = $this->resultSet();
			for($i = 0; $i < $month; ++$i)
				$login[$i] = 0;
			foreach($rs as $log) {
				$login[$log["month"] - 1] = $log["naccess"];
			}
			return $login;
		}

		public function countUsers() {
			$this->query("SELECT * FROM users;");
			return count($this->resultSet());
		}

		public function countActors() {
			$this->query("SELECT * FROM actors;");
			return count($this->resultSet());
		}

		public function countSources() {
			$this->query("SELECT * FROM sources;");
			return count($this->resultSet());
		}

		public function countUsecase() {
			$this->query("SELECT * FROM usecase;");
			return count($this->resultSet());
		}

		public function countRequirements() {
			$this->query("SELECT * FROM requirements;");
			return count($this->resultSet());
		}

		public function adjustLoginForm($page, $data) {
			if($data) {
				if(array_key_exists('groupname', $data))
					$page = str_replace(':groupname:', $data['groupname'], $page);
				$page = str_replace(':message:', '<p class="message warning">Username e/o password errati!</p>', $page);
			} else {
				$page = str_replace(':groupname:', '', $page);
				$page = str_replace(':message:', '', $page);
			}
			return $page;
		}

		public function adjustRegistrationForm($page, $data) {
			if($data) {
				if(array_key_exists('groupname', $data)) {
					$page = str_replace(':groupname:', $data['groupname'], $page);
					$page = str_replace(':message:', '<p class="message warning">Le due password non coincidono!</p>', $page);
				}
				else {
					$page = str_replace(':groupname:', '', $page);
					$page = str_replace(':message:', '<p class="message warning">Nome del gruppo già in uso!</p>', $page);
				}
				if(array_key_exists('projectname', $data))
					$page = str_replace(':projectname:', $data['projectname'], $page);
			} else {
				$page = str_replace(':groupname:', '', $page);
				$page = str_replace(':projectname:', '', $page);
				$page = str_replace(':message:', '', $page);
			}
			return $page;
		}
	}
?>

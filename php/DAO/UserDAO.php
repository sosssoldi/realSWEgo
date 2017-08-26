<?php
	class UserDAO extends Database {
		//Costruttore
		public function __construct($connection = null) {
			parent::__construct($connection);
		}
		//Metodo che inserisce un nuovo utente nel database
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
		//Metodo che verifica se l'utente che sta cercando di autenticarsi esiste oppure no
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
		//Metodo che salva nel database l'accesso (data, ora e username)
		public function saveAccess($user) {
			$this->query("INSERT INTO login VALUES({$user["id"]},NOW());");
			return $this->resultSet();
		}
		//Metodo che permette di aggiornare una password
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
		//Metodo che ritorna gli utenti presenti nel database
		public function select() {
			$this->query('SELECT * FROM users ORDER BY groupname;');
			return $this->resultSet();
		}
		//Metodo che ritorna gli accessi effettuati dai vari utenti raggruppati per mese
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
		//Metodo che ritorna il numero di utenti presenti nel database
		public function countUsers() {
			$this->query("SELECT * FROM users;");
			return count($this->resultSet());
		}
		//Metodo che ritorna il numero di attori presenti nel database
		public function countActors() {
			$this->query("SELECT * FROM actors;");
			return count($this->resultSet());
		}
		//Metodo che ritorna il numero delle fonti presenti nel database
		public function countSources() {
			$this->query("SELECT * FROM sources;");
			return count($this->resultSet());
		}
		//Metodo che ritorna il numero di usecase presenti nel database
		public function countUsecase() {
			$this->query("SELECT * FROM usecase;");
			return count($this->resultSet());
		}
		//Metodo che ritorna il numero di requisiti presenti nel database
		public function countRequirements() {
			$this->query("SELECT * FROM requirements;");
			return count($this->resultSet());
		}
		//Metodo che si occupa di fare il rendering del form con le informazioni dinamiche
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
		//Metodo che si occupa di fare il rendering del form di registrazione con le informazioni dinamiche
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

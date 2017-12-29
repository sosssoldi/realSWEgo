<?php
	include_once "DBConnection.php";

	class MySQLConnection implements DBConnection {
		private $host = "";
		private $dbname = "";
		private $user = "";
		private $pwd = "";
		private $options = "";

		public function __construct($host = "localhost", $dbname = "swego", $user = "root", $pwd = "") {
			$this->host = $host;
			$this->dbname = $dbname;
			$this->user = $user;
			$this->pwd = $pwd;
			$this->options = array(
				PDO::ATTR_PERSISTENT => true,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			);
		}

		public function connect() {
				try {
					return new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->user, $this->pwd, $this->options);
				}
				catch(PDOException $e) {
					echo "Errore di connessione";
					return null;
				}
		}

		public function gethost() {
			return $this->host;
		}

		public function getdbname() {
			return $this->dbname;
		}

		public function getuser() {
			return $this->user;
		}

		public function getpwd() {
			return $this->pwd;
		}

		public function getoptions() {
			return $this->options;
		}

		public function sethost($host = null) {
			if($host !== null)
				$this->host = $host;
		}

		public function setdbname($dbname = null) {
			if($dbname !== null)
				$this->dbname = $dbname;
		}

		public function setuser($user = null) {
			if($user !== null)
				$this->user = $user;
		}

		public function setpwd($pwd = null) {
			if($pwd !== null)
				$this->pwd = $pwd;
		}

		public function setoptions($options = null) {
			if($options !== null)
				$this->options = $options;
		}
	}
?>

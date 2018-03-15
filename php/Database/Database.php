<?php
	include_once "Connection/MySQLConnection.php";
	
	class Database {
		private $dbc = null;
		private $stmt = '';

		public function __construct($conn = null) {
			if($conn === null)
				$conn = new MySQLConnection();
			$this->dbc = $conn->connect();
		}

		public function query($query) {
			$this->stmt = $this->dbc->prepare($query);
			return $this->stmt;
		}

		public function bind($param, $value) {
			$this->stmt->bindValue($param, $value);
		}

		public function execute() {
			try {
				$rs = $this->stmt->execute();
				return $rs;
			}
			catch(PDOException $ex) {
				return 0;
			}
		}

		public function resultset() {
			try {
				$this->execute();
				return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
			}
			catch(PDOException $ex) {
				return 0;
			}
		}

		public function rowCount() {
			return $this->stmt->rowCount();
		}
	}
?>

<?php
	namespace php\Database;

	//include_once "Connection/MySQLConnection.php";
	use php\Database\Connection\MySQLConnection;
	use \PDO;
	use \PDOException;

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
			$this->execute();
			return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
		}

		public function rowCount() {
			return $this->stmt->rowCount();
		}
	}
?>
<?php 
	namespace php\DAO;

	include_once "../Database/Database.php";
	include_once "DAO.php";

	use \php\Database\Database;
	use \php\DAO\DAO;

	class SourceDAO extends Database implements DAO {

		private $source = null;

		public function __construct($connection = null, $source) {
			parent::__construct($connection);
			$this->source = $source;
		}

		public function insert() {
			$this->query('INSERT INTO sources VALUES (id, ":name", ":description")');
			$this->bind(':name', $this->source->getName());
			$this->bind(':description', $this->source->getDescription());
			return $this->resultSet();
		}

		public function update() {
			$this->query('UPDATE sources SET name = ":name", description = ":description" WHERE id = :id');
			$this->bind(':name', $this->source->getName());
			$this->bind(':description', $this->source->getDescription());
			$this->bind(':id', $this->source->getId());
			return $this->resultSet();
		}

		public function delete() {
			$this->query('DELETE FROM sources WHERE id = :id');
			$this->bind(':id', $this->source->getId());
			return $this->resultSet();
		}

		public function select() {
			$this->query('SELECT * FROM sources ORDER BY name;');
			return $this->resultSet();
		}
	}
?>

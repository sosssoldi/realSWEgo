<?php
	class ActorDAO extends Database implements DAO {
		
		private $actor = null;

		public function __construct($actor = null, $connection = null) {
			parent::__construct($connection);
			$this->actor = $actor;
		}

		public function insert() {
			$this->query('INSERT INTO actors VALUES (id, ":name", ":description")');
			$this->bind(':name', $this->actor->getName());
			$this->bind(':description', $this->actor->getDescription());
			return $this->resultSet();
		}

		public function update() {
			$this->query('UPDATE actors SET name = ":name", description = ":description" WHERE id = :id');
			$this->bind(':name', $this->actor->getName());
			$this->bind(':description', $this->actor->getDescription());
			$this->bind(':id', $this->actor->getId());
			return $this->resultSet();
		}

		public function delete() {
			$this->query('DELETE FROM actors WHERE id = :id');
			$this->bind(':id', $this->actor->getId());
			return $this->resultSet();
		}

		public function select() {
			$this->query('SELECT * FROM actors ORDER BY name;');
			return $this->resultSet();
		}
	}
?>

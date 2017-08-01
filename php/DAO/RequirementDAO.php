<?php 
	namespace php\DAO;

	include_once "../Database/Database.php";
	include_once "DAO.php";

	use \php\Database\Database;
	use \php\DAO\DAO;

	class RequirementDAO extends Database implements DAO {
		
		private $requirement = null;

		public function __construct($connection = null, $requirement) {
			parent::__construct($connection);
			$this->requirement = $requirement;
		}

		public function insert() {
			$this->query('INSERT INTO requirements VALUES (id, ":requirementid", ":description", ":type", ":importance", ":satisfied", :parent, :source)');
			$this->bind(':requirementid', $this->requirement->getRequirementId());
			$this->bind(':description', $this->requirement->getDescription());
			$this->bind(':type', $this->requirement->getType());
			$this->bind(':importance', $this->requirement->getImportance());
			$this->bind(':satisfied', $this->requirement->getSatisfied());
			$this->bind(':parent', $this->requirement->getParent());
			$this->bind(':source', $this->requirement->getSource());
			return $this->resultSet();
		}

		public function update() {
			$this->query('UPDATE requirements SET requirementid = ":requirementid", description = ":description", type = ":type", importance = ":importance", satisfied = ":satisfied", parent = :parent, source = :source WHERE id = :id');
			$this->bind(':requirementid', $this->requirement->getRequirementId());
			$this->bind(':description', $this->requirement->getDescription());
			$this->bind(':type', $this->requirement->getType());
			$this->bind(':importance', $this->requirement->getImportance());
			$this->bind(':satisfied', $this->requirement->getSatisfied());
			$this->bind(':parent', $this->requirement->getParent());
			$this->bind(':source', $this->requirement->getSource());
			$this->bind(':id', $this->requirement->getId());
			return $this->resultSet();
		}

		public function delete() {
			$this->query('DELETE FROM requirements WHERE id = :id');
			$this->bind(':id', $this->requirement->getId());
			return $this->resultSet();
		}

		public function select() {
			$this->query('SELECT * FROM requirements ORDER BY requirementid;');
			return $this->resultSet();
		}
	}
?>

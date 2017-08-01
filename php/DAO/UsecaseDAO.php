<?php 
	namespace php\DAO;

	include_once "../Database/Database.php";
	include_once "DAO.php";

	use \php\Database\Database;
	use \php\DAO\DAO;

	class UsecaseDAO extends Database implements DAO {
		
		private $usecase = null;

		public function __construct($connection = null, $usecase) {
			parent::__construct($connection);
			$this->usecase = $usecase;
		}

		public function insert() {
			$this->query('INSERT INTO usecase VALUES (id, ":usecaseid", ":name", ":description", ":precondition", ":postcondition", "mainscenario", ":alternativescenario", :generalization, :parent)');
			$this->bind(':usecaseid', $this->usecase->getusecaseId());
			$this->bind(':name', $this->usecase->getName());
			$this->bind(':description', $this->usecase->getDescription());
			$this->bind(':precondition', $this->usecase->getPrecondition());
			$this->bind(':postcondition', $this->usecase->getPostcondition());
			$this->bind(':mainscenario', $this->usecase->getMainscenario());
			$this->bind(':alternativescenario', $this->usecase->getAlternativescenario());
			$this->bind(':generalization', $this->usecase->getGeneralization());
			$this->bind(':parent', $this->usecase->getParent());
			return $this->resultSet();
		}

		public function update() {
			$this->query('UPDATE usecase SET usecaseid = ":usecaseid", name = ":name", description = ":description", precondition = ":precondition", postcondition = ":postcondition", mainscenario = ":mainscenario", alternativescenario = ":alternativescenario", generalization = :generalization, parent = :parent WHERE id = :id');
			$this->bind(':usecaseid', $this->usecase->getusecaseId());
			$this->bind(':name', $this->usecase->getName());
			$this->bind(':description', $this->usecase->getDescription());
			$this->bind(':precondition', $this->usecase->getPrecondition());
			$this->bind(':postcondition', $this->usecase->getPostcondition());
			$this->bind(':mainscenario', $this->usecase->getMainscenario());
			$this->bind(':alternativescenario', $this->usecase->getAlternativescenario());
			$this->bind(':generalization', $this->usecase->getGeneralization());
			$this->bind(':parent', $this->usecase->getParent());
			$this->bind(':id', $this->usecase->getId());
			return $this->resultSet();
		}

		public function delete() {
			$this->query('DELETE FROM usecase WHERE id = :id');
			$this->bind(':id', $this->usecase->getId());
			return $this->resultSet();
		}

		public function select() {
			$this->query('SELECT * FROM usecase ORDER BY usecaseid;');
			return $this->resultSet();
		}

		public function adjustForm($page) {
			$rs = $this->select();
			$str = "";
			while($uc = $rs->fetch_assoc()) {
				$str .= "<option value=''>asd</option>";
			}
			return str_replace(":options", $str, $page);
		}
	}
?>

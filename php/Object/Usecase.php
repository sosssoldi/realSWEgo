<?php
	include_once "Object.php";

	class Usecase extends Object {

		private $id = null;
		private $usecaseid = null;
		private $name = null;
		private $description = null;
		private $precondition = null;
		private $postcondition = null;
		private $mainscenario = null;
		private $alternativescenario = null;
		private $generalization = null;
		private $parent = null;
		
		public function __construct($obj) {
			if(array_key_exists('id', $obj))
				$this->id = $obj['id'];
			if(array_key_exists('usecaseid', $obj));
				$this->usecaseid = $obj['usecaseid'];
			if(array_key_exists('name', $obj))
				$this->name = $obj['name'];  
			if(array_key_exists('description', $obj));
				$this->description = $obj['description'];
			if(array_key_exists('precondition', $obj))
				$this->precondition = $obj['precondition'];
			if(array_key_exists('postcondition', $obj));
				$this->postcondition = $obj['postcondition'];
			if(array_key_exists('mainscenario', $obj));
				$this->mainscenario = $obj['mainscenario'];
			if(array_key_exists('alternativescenario', $obj));
				$this->alternativescenario = $obj['alternativescenario'];
			if(array_key_exists('generalization', $obj));
				$this->generalization = $obj['generalization'];
			if(array_key_exists('parent', $obj));
				$this->parent = $obj['parent'];
		}

		public function getId() {
			return $this->id;
		}

		public function getRequirementId() {
			return $this->requirementid;
		}

		public function getDescription() {
			return $this->description;
		}

		public function getPrecondition() {
			return $this->precondition;
		}

		public function getPostcondition() {
			return $this->postcondition;
		}

		public function getMainscenario() {
			return $this->mainscenario;
		}

		public function getAlternativescenario() {
			return $this->alternativescenario;
		}

		public function getGeneralization() {
			return $this->generalization;
		}

		public function getParent() {
			return $this->parent;
		}
	}
?>

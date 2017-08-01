<?php
	namespace php\Object;

	include_once "Object.php";

	class Requirement extends Object {

		private $id = null;
		private $requirementid = null;
		private $description = null;
		private $type = null;
		private $importance = null;
		private $satisfied = null;
		private $parent = null;
		private $source = null;
		
		public function __construct($obj) {
			if(array_key_exists('id', $obj))
				$this->id = $obj['id'];
			if(array_key_exists('requirementid', $obj));
				$this->requirementid = $obj['requirementid'];
			if(array_key_exists('description', $obj));
				$this->description = $obj['description'];
			if(array_key_exists('type', $obj))
				$this->type = $obj['type'];
			if(array_key_exists('importance', $obj));
				$this->importance = $obj['importance'];
			if(array_key_exists('satisfied', $obj));
				$this->satisfied = $obj['satisfied'];
			if(array_key_exists('parent', $obj));
				$this->parent = $obj['parent'];
			if(array_key_exists('source', $obj));
				$this->source = $obj['source'];
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

		public function getType() {
			return $this->type;
		}

		public function getImportance() {
			return $this->importance;
		}

		public function getSatisfied() {
			return $this->satisfied;
		}

		public function getParent() {
			return $this->parent;
		}

		public function getSource() {
			return $this->source;
		}
	}
?>

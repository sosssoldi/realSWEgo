<?php
	include_once "Object.php";

	class Actor extends Object {

		private $id = null;
		private $name = null;
		private $description = null;
		
		public function __construct($obj) {
			if(array_key_exists('id', $obj))
				$this->id = $obj['id'];
			if(array_key_exists('name', $obj));
				$this->name = $obj['name'];
			if(array_key_exists('description', $obj));
				$this->description = $obj['description'];
		}

		public function getId() {
			return $this->id;
		}

		public function getName() {
			return $this->name;
		}

		public function getDescription() {
			return $this->description;
		}
	}
?>

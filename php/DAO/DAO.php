<?php 
	namespace php\DAO;

	interface DAO {
		public function insert();
		public function update();
		public function delete();
		public function select();
	}
?>

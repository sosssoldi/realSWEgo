<?php
	class SourceDAO extends Database{

		public function __construct($connection = null) {
			parent::__construct($connection);
		}

		public function insert($obj) {
			$this->query("INSERT INTO sources VALUES (id, '{$obj['name']}', '{$obj['description']}');");
			$this->resultSet();
		}

		public function update($id, $obj) {
			$this->query("UPDATE sources SET name = '{$obj['name']}', description = '{$obj['description']}' WHERE id = {$id};");
			return $this->resultSet();
		}

		public function delete($id) {
			$this->query("DELETE FROM sources WHERE id = {$id}");
			return $this->resultSet();
		}

		public function select() {
			$this->query('SELECT * FROM sources ORDER BY name;');
			return $this->resultSet();
		}

		public function getSource($id) {
			$this->query("SELECT * FROM sources WHERE id={$id};");
			return $this->resultSet();
		}

		public function adjustForm($page, $data) {
			$rs = $this->select();
			$str = "";
			if($rs) {
				foreach($rs as $source) {
					$html = file_get_contents(__DIR__.'/../../template/viewSource.html');
					$html = str_replace(':name:', $source['name'], $html);
					$html = str_replace(':description:', $source['description'], $html);
					$html = str_replace(':id:', $source['id'], $html);
					$str .= $html;
				}
				$page = str_replace(':sources:', $str, $page);
			} else {
				$page = str_replace(':sources:', '', $page);
			}
			if($data) {
				if(array_key_exists('name', $data))
					$page = str_replace(':name:', $data['name'], $page);
				if(array_key_exists('description', $data))
					$page = str_replace(':description:', $data['description'], $page);
			} else {
				$page = str_replace(':name:', '', $page);
				$page = str_replace(':description:', '', $page);
			}
			return $page;
		}

		public function fillForm($page, $data) {
			$page = str_replace(':name:', $data["name"], $page);
			$page = str_replace(':description:', $data["description"], $page);
			return $page;
		}
	}
?>

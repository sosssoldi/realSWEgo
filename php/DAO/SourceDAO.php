<?php
	class SourceDAO extends Database{
		//Costruttore
		public function __construct($connection = null) {
			parent::__construct($connection);
		}
		//Metodo che inserisce una fonte nel database
		public function insert($obj, $projectid) {
			$this->query("INSERT INTO sources VALUES (id, '{$obj['name']}', '{$obj['description']}', {$projectid});");
			$this->resultSet();
		}
		//Metodo che modifica una fonte presente nel database
		public function update($id, $obj) {
			$this->query("UPDATE sources SET name = '{$obj['name']}', description = '{$obj['description']}' WHERE id = {$id};");
			return $this->resultSet();
		}
		//Metodo che elimina una fonte presente nel database
		public function delete($id) {
			$this->query("DELETE FROM sources WHERE id = {$id}");
			return $this->resultSet();
		}
		//Metodo che ritorna le fonti presenti nel database
		public function select($projectid) {
			$this->query("SELECT * FROM sources WHERE projectid = {$projectid} ORDER BY name;");
			return $this->resultSet();
		}
		//Metodo che ritorna le informazioni di una fonte presente nel database
		public function getSource($id, $projectid) {
			$this->query("SELECT * FROM sources WHERE id={$id} AND projectid = {$projectid};");
			return $this->resultSet();
		}
		//Metodo che si occupa di fare il rendering del form con le informazioni dinamiche
		public function adjustForm($page, $data, $projectid) {
			$rs = $this->select($projectid);
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
				$page = str_replace(':sources:', 'Non ci sono fonti.', $page);
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
		//Metodo che riempie il form con le informazioni della fonte
		public function fillForm($page, $data) {
			$page = str_replace(':name:', $data["name"], $page);
			$page = str_replace(':description:', $data["description"], $page);
			return $page;
		}
	}
?>

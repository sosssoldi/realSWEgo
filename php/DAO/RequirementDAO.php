<?php
	class RequirementDAO extends Database {

		public function __construct($connection = null) {
			parent::__construct($connection);
		}

		public function insert($obj, $projectid) {
			if($obj['parent'] == 'NULL')
				$id = $this->getIdWithoutParent($obj['importance'], $obj['type'], $projectid);
			else
				$id = $this->getIdWithParent($obj['parent'], $obj['importance'], $obj['type'], $projectid);
			$importance = array("Obbligatorio","Desiderabile","Opzionale");
			$type = array("F"=>"Funzionale","Q"=>"Di Qualità","P"=>"Prestazionale","V"=>"Di Vincolo");
			$importance = $importance[$obj["importance"]];
			$type = $type[$obj["type"]];
			$this->query("INSERT INTO requirements VALUES (id, '{$id}', '{$obj['description']}', '{$type}', '{$importance}', '{$obj['satisfied']}', {$obj['parent']}, {$obj['source']}, {$projectid});");
			return $this->resultSet();
		}

		public function getIdWithoutParent($importance, $type, $projectid) {
			$this->query("SELECT requirementid FROM requirements WHERE parent IS NULL AND type LIKE '%{$type}%' AND projectid = {$projectid} ORDER BY id desc LIMIT 1;");
			$rs = $this->resultSet();
			if(!$rs)
				$id = "R".$importance.$type."1";
			else {
				$requisito = $rs[0];
				$lastdigit = substr($requisito["requirementid"], 3) + 1;
				$id = "R".$importance.$type.$lastdigit;
			}
			return $id;
		}

		public function getIdWithParent($parent, $importance, $type, $projectid) {
			$this->query("SELECT requirementid FROM requirements WHERE parent={$parent} AND projectid = {$projectid} ORDER BY length(requirementid) desc, requirementid DESC;");
			$rs = $this->resultSet();
			if(!$rs) {
				$this->query("SELECT requirementid FROM requirements WHERE id={$parent};");
				$rs = $this->resultSet();
				$requisito = $rs[0];
				$requisito["requirementid"] = substr($requisito["requirementid"], 3);
				$id = "R".$importance.$type.$requisito["requirementid"].".1";
				
			} else {
				$maxnumber = -1;
				$idreq = null;
				foreach($rs as $requisito) {
					$pieces = explode(".",$requisito["requirementid"]);
					if($pieces[count($pieces) - 1] > $maxnumber) {
						$maxnumber = $pieces[count($pieces) - 1];
						$idreq = "";
						for($i = 0; $i < count($pieces) - 1; ++$i)
							$idreq .= $pieces[$i].".";
					}
				}
				$maxnumber++;
				$idreq = substr($idreq, 3);
				$id = "R".$importance.$type.$idreq.$maxnumber;
			}
			return $id;
		}

		public function update($id, $obj, $projectid) {
			$lid = $obj["requirementid"];
			$nid = "";
			if($obj["parent"] != $this->getParent($id))
				if($obj['parent'] == 'NULL')
					$nid = $this->getIdWithoutParent($obj['importance'], $obj['type'], $projectid);
				else
					$nid = $this->getIdWithParent($obj['parent'], $obj['importance'], $obj['type'], $projectid);
			else {
				$requirementid = substr($lid,3);
				$nid = "R".$obj["importance"].$obj["type"].$requirementid;
			}
			$importance = array("Obbligatorio","Desiderabile","Opzionale");
			$type = array("F"=>"Funzionale","Q"=>"Di Qualità","P"=>"Prestazionale","V"=>"Di Vincolo");
			$importance = $importance[$obj["importance"]];
			$type = $type[$obj["type"]];
			$this->query("UPDATE requirements SET requirementid = '{$nid}', description = '{$obj['description']}', type = '{$type}', importance = '{$importance}', satisfied = '{$obj['satisfied']}', parent = {$obj['parent']}, source = {$obj['source']} WHERE id = {$id}");
			echo "UPDATE requirements SET requirementid = '{$nid}', description = '{$obj['description']}', type = '{$type}', importance = '{$importance}', satisfied = '{$obj['satisfied']}', parent = {$obj['parent']}, source = {$obj['source']} WHERE id = {$id}";
			$this->resultSet();
			$this->fix($id, $lid, $nid);
		}

		public function fix($id, $lid, $nid) {
			if($nid != $lid) {
				$length = strlen($lid);
				$array = array();
				$array = $this->getHierarchy($id);
				foreach($array as $requirement) {
					$rid = substr($requirement["requirementid"], $length);
					$rid = $nid.$rid;
					$this->query("UPDATE requirements SET requirementid = '{$rid}' WHERE id = {$requirement['id']};");
					echo "UPDATE requirements SET requirementid = '{$rid}' WHERE id = {$requirement['id']};";
					$this->resultSet();
				}
			}
		}

		public function delete($id) {
			$this->query("DELETE FROM requirements WHERE id = {$id};");
			return $this->resultSet();
		}

		public function select($projectid) {
			$this->query("SELECT * FROM requirements WHERE projectid = {$projectid} ORDER BY requirementid;");
			return $this->resultSet();
		}

		public function selectTrackedRequirements($projectid) {
			$this->query("SELECT requirementid FROM usecaserequirements WHERE requirementid IN (SELECT id FROM requirements WHERE projectid = {$projectid});");
			return $this->resultSet();
		}

		public function getRequirement($id, $projectid) {
			$this->query("SELECT * FROM requirements WHERE id = {$id} AND projectid = {$projectid};");
			return $this->resultSet();
		}

		public function getRequirementImportanceCount($projectid) {
			$this->query("SELECT * FROM requirements WHERE importance='Obbligatorio' AND satisfied='Non implementato' AND projectid = {$projectid};");
			$u0n = count($this->resultSet());
			$this->query("SELECT * FROM requirements WHERE importance='Obbligatorio' AND satisfied='Implementato' AND projectid = {$projectid};");
			$s0n = count($this->resultSet());
			$this->query("SELECT * FROM requirements WHERE importance='Desiderabile' AND satisfied='Non implementato' AND projectid = {$projectid};");
			$u1n = count($this->resultSet());
			$this->query("SELECT * FROM requirements WHERE importance='Desiderabile' AND satisfied='Implementato' AND projectid = {$projectid};");
			$s1n = count($this->resultSet());
			$this->query("SELECT * FROM requirements WHERE importance='Opzionale' AND satisfied='Non implementato' AND projectid = {$projectid};");
			$u2n = count($this->resultSet());
			$this->query("SELECT * FROM requirements WHERE importance='Opzionale' AND satisfied='Implementato' AND projectid = {$projectid};");
			$s2n = count($this->resultSet());
			return array($s0n, $u0n, $s1n, $u1n, $s2n, $u2n);
		}

		function getParent($id) {
			$this->query("SELECT parent FROM requirements WHERE id={$id};");
			$rs = $this->resultSet();
			if($rs) {
				$requirement = $rs[0];
				return $requirement["parent"];
			} else
				return NULL;	
		}

		function getHierarchy($id) {
			$unchecked = array();
			$checked = array();
			$unchecked = array_merge($unchecked, $this->getDirectChildren($id));
			while(!empty($unchecked)) {
				$element = array_shift($unchecked);
				$unchecked = array_merge($unchecked, $this->getDirectChildren($element['id']));
				$checked[] = $element;
			}
			return $checked;
		}
		
		function getDirectChildren($id) {
			$this->query("SELECT * FROM requirements WHERE parent={$id};");
			$rs = $this->resultSet();
			$array = array();
			foreach($rs as $requirement)
				$array[] = $requirement;
			return $array;
		}

		public function selectSources($projectid) {
			$this->query("SELECT * FROM sources WHERE projectid = {$projectid} ORDER BY name;");
			return $this->resultSet();
		}

		public function adjustForm($page, $data, $projectid) {
			$rs = $this->select($projectid);
			$str = "";
			if($rs) {
				foreach($rs as $requirement)
					$str .= '<option value="'.$requirement['id'].'">'.$requirement['requirementid'].'-'.$requirement['description'].'</option>';
				$page = str_replace(":requirementoptions:", $str, $page);
			} else
				$page = str_replace(":requirementoptions:", "", $page);
			$rs = $this->selectSources($projectid);
			$str = "";
			if($rs) {
				foreach($rs as $source)
					$str .= '<option value="'.$source['id'].'">'.$source['name'].'</option>';
				$page = str_replace(":sourceoptions:", $str, $page);
			} else
				$page = str_replace(":sourceoptions:", "", $page);
			if($data) {
				if(array_key_exists('description', $data))
					if($data['description'] != '') {
						$page = str_replace(':description:', '', $page);
						$page = str_replace(':message:', '<p class="message success">Requisito inserito!</p>', $page);
					}
					else {
						$page = str_replace(':description:', $data['description'], $page);
						$page = str_replace(':message:', '<p class="message warning">Riempire tutti i campi!</p>', $page);
					}
			} else {
				$page = str_replace(':description:', '', $page);
				$page = str_replace(':message:', '', $page);
			}
			return $page;
		}

		public function find($value, $array) {
			$found = false;
			for($i = 0; $i < count($array) && !$found; ++$i)
				if($array[$i] == $value)
					$found = true;
			return $found;
		}

		public function fillForm($page, $data, $projectid) {
			$page = str_replace(':requirementid:', $data["requirementid"], $page);
			$page = str_replace(':description:', $data["description"], $page);
			$rs = $this->select($projectid);
			$str = "";
			if($rs) {
				foreach($rs as $requirement)
					if($requirement["id"] == $data["parent"])
						$str .= '<option value="'.$requirement['id'].'" selected="selected">'.$requirement['requirementid'].'-'.$requirement['description'].'</option>';
					else
						$str .= '<option value="'.$requirement['id'].'">'.$requirement['requirementid'].'-'.$requirement['description'].'</option>';
				$page = str_replace(":parentoptions:", $str, $page);
			} else
				$page = str_replace(":parentoptions:", "", $page);
			$str = "";
			if($data["type"] == "Funzionale") {
				$str = '<option value="F" selected="selected">F - Funzionale</option>';
				$str .= '<option value="Q">Q - Di Qualità</option>';
				$str .= '<option value="P">P - Prestazionale</option>';
				$str .= '<option value="V">V - Di Vincolo</option>';
			} else if($data["type"] == "Di Qualità") {
				$str = '<option value="F">F - Funzionale</option>';
				$str .= '<option value="Q" selected="selected">Q - Di Qualità</option>';
				$str .= '<option value="P">P - Prestazionale</option>';
				$str .= '<option value="V">V - Di Vincolo</option>';
			} else if($data["type"] == "Prestazionale") {
				$str = '<option value="F">F - Funzionale</option>';
				$str .= '<option value="Q">Q - Di Qualità</option>';
				$str .= '<option value="P" selected="selected">P - Prestazionale</option>';
				$str .= '<option value="V">V - Di Vincolo</option>';
			} else if($data["type"] == "Di Vincolo") {
				$str = '<option value="F">F - Funzionale</option>';
				$str .= '<option value="Q">Q - Di Qualità</option>';
				$str .= '<option value="P">P - Prestazionale</option>';
				$str .= '<option value="V" selected="selected">V - Di Vincolo</option>';
			}
			$page = str_replace(':type:', $str, $page);
			$str = "";
			if($data["importance"] == "Obbligatorio") {
				$str = '<option value="0" selected="selected">0 - Obbligatorio</option>';
				$str .= '<option value="1">1 - Desiderabile</option>';
				$str .= '<option value="2">2 - Opzionale</option>';
			} else if($data["importance"] == "Desiderabile") {
				$str = '<option value="0">0 - Obbligatorio</option>';
				$str .= '<option value="1" selected="selected">1 - Desiderabile</option>';
				$str .= '<option value="2">2 - Opzionale</option>';
			} else if($data["importance"] == "Opzionale") {
				$str = '<option value="0">0 - Obbligatorio</option>';
				$str .= '<option value="1">1 - Desiderabile</option>';
				$str .= '<option value="2" selected="selected">2 - Opzionale</option>';
			}
			$page = str_replace(':importance:', $str, $page);
			$str = "";
			if($data["satisfied"] == "Non implementato") {
				$str = '<option value="Non implementato" selected="selected">Non Implementato</option>';
				$str .= '<option value="Implementato">Implementato</option>';
			} else {
				$str = '<option value="Non implementato">Non Implementato</option>';
				$str .= '<option value="Implementato" selected="selected">Implementato</option>';
			}
			$page = str_replace(':satisfied:', $str, $page);
			$str = "";
			$rs = $this->selectSources($projectid);
			if($rs) {
				foreach($rs as $source)
					if($source["id"] == $data["source"])
						$str .= '<option value="'.$source['id'].'" selected="selected">'.$source['name'].'</option>';
					else
						$str .= '<option value="'.$source['id'].'">'.$source['name'].'</option>';
				$page = str_replace(":sourceoptions:", $str, $page);
			} else
				$page = str_replace(":sourceoptions:", "", $page);
			return $page;
		}
	}
?>

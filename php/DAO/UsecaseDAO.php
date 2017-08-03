<?php
	class UsecaseDAO extends Database {

		public function __construct($connection = null) {
			parent::__construct($connection);
		}

		public function insert($obj) {
			if($obj['parent'] == 'NULL')
				$id = $this->getIdWithoutParent();
			else
				$id = $this->getIdWithParent($obj['parent']);
			$this->query("INSERT INTO usecase VALUES (id, '{$id}', '{$obj['name']}', '{$obj['description']}', '{$obj['precondition']}', '{$obj['postcondition']}', '{$obj['mainscenario']}', '{$obj['alternativescenario']}', {$obj['generalization']}, {$obj['parent']})");
			$this->resultSet();
			$this->query("SELECT id FROM usecase WHERE usecaseid='{$id}';");
			$rs = $this->resultSet();
			if($rs)
				$id = $rs[0]['id'];
			foreach($obj['inclusion'] as $includedusecaseid) {
				$this->query("INSERT INTO usecaseinclusions VALUES ({$id}, {$includedusecaseid})");
				$this->resultSet();
			}
			foreach($obj['extension'] as $extendedusecaseid) {
				$this->query("INSERT INTO usecaseextensions VALUES ({$id}, {$extendedusecaseid})");
				$this->resultSet();
			}
			foreach($obj['actor'] as $actorid) {
				$this->query("INSERT INTO usecaseactors VALUES ({$id}, {$actorid})");
				$this->resultSet();
			}
		}

		public function getIdWithoutParent() {
			$this->query("SELECT usecaseid FROM usecase WHERE parent IS NULL ORDER BY usecaseid DESC LIMIT 1;");
			$rs = $this->resultSet();
			if(!$rs)
				$id = "UC1";
			else {
				$usecase = $rs[0];
				$number = substr($usecase["usecaseid"], 2) + 1;
				$id = "UC".$number;
			}
			return $id;
		}

		public function getIdWithParent($parent) {
			$this->query("SELECT usecaseid FROM usecase WHERE parent={$parent} ORDER BY length(usecaseid) desc, usecaseid DESC LIMIT 1;");
			$rs = $this->resultSet();
			if(!$rs) {
				$this->query("SELECT usecaseid FROM usecase WHERE id={$parent};");
				$rs = $this->resultSet();
				$usecase = $rs[0];
				$id = $usecase["usecaseid"].".1";
			} else {
				$usecase = $rs[0];
				$numbers = explode(".", $usecase["usecaseid"]);
				$lastdigit = $numbers[count($numbers) - 1] + 1;
				$id = "";
				for($i = 0; $i < count($numbers) - 1; ++$i)
					$id .= $numbers[$i].".";
				$id .= $lastdigit; 
			}
			return $id;
		}

		public function update($id, $obj) {
			$lid = $obj["usecaseid"];
			$nid = "";
			if($obj["parent"] != $this->getParent($id))
				if($obj['parent'] == 'NULL')
					$nid = $this->getIdWithoutParent();
				else
					$nid = $this->getIdWithParent($obj['parent']);
			else
				$nid = $lid;
			$this->query("UPDATE usecase SET usecaseid = '{$nid}', name = '{$obj['name']}', description = '{$obj['description']}', precondition = '{$obj['precondition']}', postcondition = '{$obj['postcondition']}', mainscenario = '{$obj['mainscenario']}', alternativescenario = '{$obj['alternativescenario']}', generalization = {$obj['generalization']}, parent = {$obj['parent']} WHERE id = {$id};");
			$this->resultSet();
			$this->query("DELETE FROM usecaseinclusions WHERE usecaseid = {$id};");
			$this->resultSet();
			foreach($obj['inclusion'] as $includedusecaseid) {
				$this->query("INSERT INTO usecaseinclusions VALUES ({$id}, {$includedusecaseid})");
				$this->resultSet();
			}
			$this->query("DELETE FROM usecaseextensions WHERE usecaseid = {$id};");
			$this->resultSet();
			foreach($obj['extension'] as $extendedusecaseid) {
				$this->query("INSERT INTO usecaseextensions VALUES ({$id}, {$extendedusecaseid})");
				$this->resultSet();
			}
			$this->query("DELETE FROM usecaseactors WHERE usecaseid = {$id};");
			$this->resultSet();
			foreach($obj['actor'] as $actorid) {
				$this->query("INSERT INTO usecaseactors VALUES ({$id}, {$actorid})");
				$this->resultSet();
			}
			$this->fix($id, $lid, $nid);
		}

		public function fix($id, $lid, $nid) {
			if($nid != $lid) {
				$length = strlen($lid);
				$array = array();
				$array = $this->getHierarchy($id);
				foreach($array as $uc) {
					$ucid = substr($uc["usecaseid"], $length);
					$ucid = $nid.$ucid;
					$this->query("UPDATE usecase SET usecaseid = '{$ucid}' WHERE id = {$uc['id']};");
					$this->resultSet();
				}
			}
		}

		public function delete($id) {
			$this->query("DELETE FROM usecase WHERE id = {$id};");
			return $this->resultSet();
		}

		public function select() {
			$this->query('SELECT * FROM usecase ORDER BY usecaseid;');
			return $this->resultSet();
		}

		public function getUsecase($id) {
			$this->query("SELECT * FROM usecase WHERE id={$id};");
			return $this->resultSet();
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
			$this->query("SELECT * FROM usecase WHERE parent={$id};");
			$rs = $this->resultSet();
			$array = array();
			foreach($rs as $uc)
				$array[] = $uc;
			return $array;
		}

		function getParent($id) {
			$this->query("SELECT parent FROM usecase WHERE id={$id};");
			$rs = $this->resultSet();
			if($rs) {
				$usecase = $rs[0];
				return $usecase["parent"];
			} else
				return NULL;	
		}

		public function getInclusions($id) {
			$this->query("SELECT * FROM usecaseinclusions WHERE usecaseid={$id} OR includedusecaseid={$id};");
			return $this->resultSet();
		}

		public function getExtensions($id) {
			$this->query("SELECT * FROM usecaseextensions WHERE usecaseid={$id} OR extendedusecaseid={$id};");
			return $this->resultSet();
		}

		public function getMyInclusions($id) {
			$this->query("SELECT * FROM usecaseinclusions WHERE usecaseid={$id};");
			return $this->resultSet();
		}

		public function getMyExtensions($id) {
			$this->query("SELECT * FROM usecaseextensions WHERE usecaseid={$id};");
			return $this->resultSet();
		}

		public function getActors($id) {
			$this->query("SELECT * FROM usecaseactors WHERE usecaseid={$id};");
			return $this->resultSet();
		}

		public function selectActors() {
			$this->query('SELECT * FROM actors ORDER BY name;');
			return $this->resultSet();
		}

		public function selectRequirements() {
			$this->query('SELECT * FROM requirements ORDER BY requirementid;');
			return $this->resultSet();
		}

		public function adjustForm($page, $data) {
			$rs = $this->select();
			$str = "";
			if($rs) {
				foreach($rs as $uc)
					$str .= '<option value="'.$uc['id'].'">'.$uc['usecaseid'].'-'.$uc['name'].'</option>';
				$page = str_replace(":usecaseoptions", $str, $page);
			} else
				$page = str_replace(":usecaseoptions", "", $page);
			$rs = $this->selectActors();
			$str = "";
			if($rs) {
				foreach($rs as $actor)
					$str .= '<option value="'.$actor['id'].'">'.$actor['name'].'</option>';
				$page = str_replace(":actoroptions", $str, $page);
			} else
				$page = str_replace(":actoroptions", "", $page);
			if($data) {
				if(array_key_exists('name', $data))
					$page = str_replace(':name:', $data['name'], $page);
				if(array_key_exists('description', $data))
					$page = str_replace(':description:', $data['description'], $page);
				if(array_key_exists('precondition', $data))
					$page = str_replace(':precondition:', $data['precondition'], $page);
				if(array_key_exists('postcondition', $data))
					$page = str_replace(':postcondition:', $data['postcondition'], $page);
				if(array_key_exists('mainscenario', $data))
					$page = str_replace(':mainscenario:', $data['mainscenario'], $page);
				if(array_key_exists('alternativescenario', $data))
					$page = str_replace(':alternativescenario:', $data['alternativescenario'], $page);
			} else {
				$page = str_replace(':name:', '', $page);
				$page = str_replace(':description:', '', $page);
				$page = str_replace(':precondition:', '', $page);
				$page = str_replace(':postcondition:', '', $page);
				$page = str_replace(':mainscenario:', '', $page);
				$page = str_replace(':alternativescenario:', '', $page);
			}
			return $page;
		}

		public function adjustTrackingForm($page, $data) {
			$rs = $this->select();
			$str = "";
			if($rs) {
				foreach($rs as $uc)
					$str .= '<option value="'.$uc['id'].'">'.$uc['usecaseid'].'-'.$uc['name'].'</option>';
				$page = str_replace(":usecaseoptions", $str, $page);
			} else
				$page = str_replace(":usecaseoptions", "", $page);
			$rs = $this->selectRequirements();
			$str = "";
			if($rs) {
				foreach($rs as $r)
					$str .= '<option value="'.$r['id'].'">'.$r['requirementid'].'-'.$r['description'].'</option>';
				$page = str_replace(":requirementoptions", $str, $page);
			} else
				$page = str_replace(":requirementoptions", "", $page);
			return $page;
		}

		public function track($obj) {
			$this->query("DELETE FROM usecaserequirements WHERE usecaseid = {$obj["usecase"]};");
			$this->resultSet();
			foreach($obj["requirement"] as $requirement) {
				$this->query("INSERT INTO usecaserequirements VALUES ({$obj["usecase"]}, {$requirement});");
				$this->resultSet();
			}
		}

		public function find($value, $array) {
			$found = false;
			for($i = 0; $i < count($array) && !$found; ++$i)
				if($array[$i] == $value)
					$found = true;
			return $found;
		}

		public function fillForm($page, $data) {
			$page = str_replace(':usecaseid:', $data["usecaseid"], $page);
			$page = str_replace(':name:', $data["name"], $page);
			$page = str_replace(':description:', $data["description"], $page);
			$page = str_replace(':precondition:', $data["precondition"], $page);
			$page = str_replace(':postcondition:', $data["postcondition"], $page);
			$page = str_replace(':mainscenario:', $data["mainscenario"], $page);
			$page = str_replace(':alternativescenario:', $data["alternativescenario"], $page);
			$rs = $this->select();
			$str = "";
			if($rs) {
				foreach($rs as $uc)
					if($uc["id"] == $data["parent"])
						$str .= '<option value="'.$uc['id'].'" selected="selected">'.$uc['usecaseid'].'-'.$uc['name'].'</option>';
					else
						$str .= '<option value="'.$uc['id'].'">'.$uc['usecaseid'].'-'.$uc['name'].'</option>';
				$page = str_replace(":parentoptions", $str, $page);
			} else
				$page = str_replace(":parentoptions", "", $page);
			if($data["generalization"]) {
				$str = '<option value="false">No</option>';
				$str .= '<option value="true" selected="selected">Si</option>';
			} else {
				$str = '<option value="false" selected="selected">No</option>';
				$str .= '<option value="true">Si</option>';
			}
			$page = str_replace(':generalizationoptions', $str, $page);
			$rs = $this->getMyInclusions($data["id"]);
			$str = "";
			$array = array();
			foreach($rs as $inclusion)
				$array[] = $inclusion["includedusecaseid"];
			$rs = $this->select();
			if($rs) {
				foreach($rs as $uc)
					if($this->find($uc["id"], $array))
						$str .= '<option value="'.$uc['id'].'" selected="selected">'.$uc['usecaseid'].'-'.$uc['name'].'</option>';
					else
						$str .= '<option value="'.$uc['id'].'">'.$uc['usecaseid'].'-'.$uc['name'].'</option>';
				$page = str_replace(":inclusionoptions", $str, $page);
			} else
				$page = str_replace(":inclusionoptions", "", $page);
			$rs = $this->getMyExtensions($data["id"]);
			$str = "";
			$array = array();
			foreach($rs as $extension)
				$array[] = $extension["extendedusecaseid"];
			$rs = $this->select();
			if($rs) {
				foreach($rs as $uc)
					if($this->find($uc["id"], $array))
						$str .= '<option value="'.$uc['id'].'" selected="selected">'.$uc['usecaseid'].'-'.$uc['name'].'</option>';
					else
						$str .= '<option value="'.$uc['id'].'">'.$uc['usecaseid'].'-'.$uc['name'].'</option>';
				$page = str_replace(":extensionoptions", $str, $page);
			} else
				$page = str_replace(":extensionoptions", "", $page);
			$rs = $this->getActors($data["id"]);
			$str = "";
			$array = array();
			foreach($rs as $actor)
				$array[] = $actor["actorsid"];
			$rs = $this->selectActors();
			if($rs) {
				foreach($rs as $actor)
					if($this->find($actor["id"], $array))
						$str .= '<option value="'.$actor['id'].'" selected="selected">'.$actor['name'].'</option>';
					else
						$str .= '<option value="'.$actor['id'].'">'.$actor['name'].'</option>';
				$page = str_replace(":actoroptions", $str, $page);
			} else
				$page = str_replace(":actoroptions", "", $page);
			return $page;
		}
	}
?>

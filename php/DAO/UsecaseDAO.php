<?php
	class UsecaseDAO extends Database {
		//Costruttore
		public function __construct($connection = null) {
			parent::__construct($connection);
		}
		//Metodo che inserisce uno usecase nel database
		public function insert($obj, $projectid) {
			if($obj["parent"] == '')
				$obj["parent"] = 'NULL';
			if($obj['parent'] == 'NULL') {
				$id = $this->getIdWithoutParent($projectid);
			} else
				$id = $this->getIdWithParent($obj['parent'], $projectid);
			$this->query("INSERT INTO usecase VALUES (id, '{$id}', '{$obj['name']}', '{$obj['description']}', '{$obj['precondition']}', '{$obj['postcondition']}', '{$obj['mainscenario']}', '{$obj['alternativescenario']}', {$obj['parent']}, {$projectid})");

			$this->resultSet();
			$this->query("SELECT id FROM usecase WHERE usecaseid='{$id}' AND projectid = {$projectid};");
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
			foreach($obj['generalization'] as $usecasegeneralizationsid) {
				$this->query("INSERT INTO usecasegeneralizations VALUES ({$id}, {$usecasegeneralizationsid})");
				$this->resultSet();
			}
			foreach($obj['actor'] as $actorid) {
				$this->query("INSERT INTO usecaseactors VALUES ({$id}, {$actorid})");
				$this->resultSet();
			}
		}
		//Metodo che ritorna il nuovo id dello usecase (senza padre)
		public function getIdWithoutParent($projectid) {
			$this->query("SELECT usecaseid FROM usecase WHERE parent IS NULL AND projectid = {$projectid} ORDER BY LENGTH(usecaseid) DESC, usecaseid DESC LIMIT 1;");
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
		//Metodo che ritorna il nuovo id dello usecase (con padre)
		public function getIdWithParent($parent, $projectid) {
			$this->query("SELECT usecaseid FROM usecase WHERE parent={$parent} AND projectid = {$projectid} ORDER BY length(usecaseid) desc, usecaseid DESC LIMIT 1;");
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
		//Metodo che modifica uno usecase presente nel database
		public function update($id, $obj, $projectid) {
			$lid = $obj["usecaseid"];
			$nid = "";
			if($id == $obj["parent"])
				$obj["parent"] = 'NULL';
			if($obj['parent'] == '')
				$obj["parent"] = 'NULL';
			if($obj["parent"] != $this->getParent($id))
				if($obj['parent'] == 'NULL')
					$nid = $this->getIdWithoutParent($projectid);
				else
					$nid = $this->getIdWithParent($obj['parent'], $projectid);
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
			$this->query("DELETE FROM usecasegeneralizations WHERE usecaseid = {$id};");
			$this->resultSet();
			foreach($obj['generalization'] as $usecasegeneralizationsid) {
				$this->query("INSERT INTO usecasegeneralizations VALUES ({$id}, {$usecasegeneralizationsid})");
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
		//Metodo che ri-calcola gli id degli usecase figli sulla base dello usecase appena modificato
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
		//Metodo che elimina uno usecase presente nel database
		public function delete($id) {
			$this->query("DELETE FROM usecase WHERE id = {$id};");
			return $this->resultSet();
		}
		//Metodo che ritorna gli usecase presenti nel database
		public function select($projectid) {
			$this->query("SELECT * FROM usecase WHERE projectid = {$projectid} ORDER BY usecaseid;");
			return $this->resultSet();
		}
		//Metodo che ritorna le informazioni di uno usecase presente nel database
		public function getUsecase($id, $projectid) {
			$this->query("SELECT * FROM usecase WHERE id={$id} AND projectid = {$projectid};");
			return $this->resultSet();
		}
		//Metodo che ritorna la gerarchia dello usecase corrente (usecase figli, figli dei figli, ecc...)
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
		//Metodo che ritorna i figli diretti dello usecase corrente
		function getDirectChildren($id) {
			$this->query("SELECT * FROM usecase WHERE parent={$id};");
			$rs = $this->resultSet();
			$array = array();
			foreach($rs as $uc)
				$array[] = $uc;
			return $array;
		}
		//Metodo che ritorna l'id del padre dello usecase corrente
		function getParent($id) {
			$this->query("SELECT parent FROM usecase WHERE id={$id};");
			$rs = $this->resultSet();
			if($rs) {
				$usecase = $rs[0];
				if($usecase["parent"] == "")
					return 'NULL';
				else
					return $usecase["parent"];
			} else
				return 'NULL';
		}

		//Metodo che ritorna le generalization dello usecase corrente
		public function getGeneralizations($id) {
			$this->query("SELECT * FROM usecasegeneralizations WHERE usecaseid={$id} OR generalizationusecaseid={$id};");
			return $this->resultSet();
		}
		//Metodo che ritorna le generalization dello usecase corrente
		public function getMyGeneralizations($id) {
			$this->query("SELECT * FROM usecasegeneralizations WHERE usecaseid={$id};");
			return $this->resultSet();
		}

		//Metodo che ritorna le inclusioni dello usecase corrente
		public function getInclusions($id) {
			$this->query("SELECT * FROM usecaseinclusions WHERE usecaseid={$id} OR includedusecaseid={$id};");
			return $this->resultSet();
		}
		//Metodo che ritorna le estensioni in cui � coinvolto lo usecase corrente
		public function getExtensions($id) {
			$this->query("SELECT * FROM usecaseextensions WHERE usecaseid={$id} OR extendedusecaseid={$id};");
			return $this->resultSet();
		}
		//Metodo che ritorna le inclusioni dello usecase corrente
		public function getMyInclusions($id) {
			$this->query("SELECT * FROM usecaseinclusions WHERE usecaseid={$id};");
			return $this->resultSet();
		}
		//Metodo che ritorna le informazioni delle inclusioni dello usecase corrente
		public function getMyInclusionsInfo($id) {
			$this->query("SELECT id, usecase.usecaseid as usecaseid, name FROM usecaseinclusions, usecase WHERE usecaseinclusions.usecaseid={$id} AND usecase.id = usecaseinclusions.includedusecaseid;");
			return $this->resultSet();
		}
		//Metodo che ritorna le estensioni dello usecase corrente
		public function getMyExtensions($id) {
			$this->query("SELECT * FROM usecaseextensions WHERE usecaseid={$id};");
			return $this->resultSet();
		}
		//Metodo che ritorna le informazioni delle estensioni dello usecase corrente
		public function getMyExtensionsInfo($id) {
			$this->query("SELECT id, usecase.usecaseid as usecaseid, name FROM usecaseextensions, usecase WHERE usecaseextensions.usecaseid={$id} AND usecase.id = usecaseextensions.extendedusecaseid;");
			return $this->resultSet();
		}
		//Metodo che ritorna gli attori relativi ad uno usecase presenti nel database
		public function getActors($id) {
			$this->query("SELECT * FROM usecaseactors WHERE usecaseid={$id};");
			return $this->resultSet();
		}
		//Metodo che ritorna gli attori presenti nel database
		public function selectActors($projectid) {
			$this->query("SELECT * FROM actors WHERE projectid = {$projectid} ORDER BY name;");
			return $this->resultSet();
		}
		//Metodo che ritorna i requisiti presenti nel database
		public function selectRequirements($projectid) {
			$this->query("SELECT * FROM requirements WHERE projectid = {$projectid} ORDER BY requirementid;");
			return $this->resultSet();
		}
		//Metodo che si occupa di fare il rendering del form con le informazioni dinamiche
		public function adjustForm($page, $data, $projectid) {
			$rs = $this->select($projectid);
			$str = "";
			$i = 1;
			if($rs) {
				foreach($rs as $uc)
					$str .= '<option value="'.$uc['id'].'">'.$uc['usecaseid'].' - '.$uc['name'].'</option>';
				$page = str_replace(":parentoptions:", $str, $page);
			} else
				$page = str_replace(":parentoptions:", "", $page);
			$str = '<div class="multiple">';
			$str .= '<input id="checkbox0" type="checkbox" name="generalization[]" value="NULL" checked="checked" />';
			$str .= '<label for="checkbox0">Nessuno</label>';
			if($rs) {
				foreach($rs as $uc) {
					$str .= '<input id="checkbox'.$i.'" type="checkbox" name="generalization[]" value="'.$uc["id"].'" />';
					$str .= '<label for="checkbox'.$i.'">'.$uc["usecaseid"].' - '.$uc["name"].'</label>';
					++$i;
				}
			}
			$str .= '</div>';
			$page = str_replace(":generalizationoptions:", $str, $page);

			$str = '<div class="multiple">';
			$str .= '<input id="checkbox0" type="checkbox" name="inclusion[]" value="NULL" checked="checked" />';
			$str .= '<label for="checkbox0">Nessuno</label>';
			if($rs) {
				foreach($rs as $uc) {
					$str .= '<input id="checkbox'.$i.'" type="checkbox" name="inclusion[]" value="'.$uc["id"].'" />';
					$str .= '<label for="checkbox'.$i.'">'.$uc["usecaseid"].' - '.$uc["name"].'</label>';
					++$i;
				}
			}
			$str .= '</div>';
			$page = str_replace(":inclusionoptions:", $str, $page);
			$str = '<div class="multiple">';
			$str .= '<input id="checkbox{$i}" type="checkbox" name="extension[]" value="NULL" checked="checked" />';
			$str .= '<label for="checkbox{$i}">Nessuno</label>';
			++$i;
			if($rs) {
				foreach($rs as $uc) {
					$str .= '<input id="checkbox'.$i.'" type="checkbox" name="extension[]" value="'.$uc["id"].'" />';
					$str .= '<label for="checkbox'.$i.'">'.$uc["usecaseid"].' - '.$uc["name"].'</label>';
					++$i;
				}
			}
			$str .= '</div>';
			$page = str_replace(":extensionoptions:", $str, $page);
			$rs = $this->selectActors($projectid);
			$str = '<div class="multiple">';
			$str .= '<input id="checkbox{$i}" type="checkbox" name="actor[]" value="NULL" checked="checked" />';
			$str .= '<label for="checkbox{$i}">Nessuno</label>';
			++$i;
			if($rs) {
				foreach($rs as $actor) {
					$str .= '<input id="checkbox'.$i.'" type="checkbox" name="actor[]" value="'.$actor["id"].'" />';
					$str .= '<label for="checkbox'.$i.'">'.$actor["name"].'</label>';
					++$i;
				}
			}
			$str .= "</div>";
			$page = str_replace(":actoroptions:", $str, $page);
			if($data) {
				if($data == 'insert') {
					$page = str_replace(':name:', '', $page);
					$page = str_replace(':description:', '', $page);
					$page = str_replace(':precondition:', '', $page);
					$page = str_replace(':postcondition:', '', $page);
					$page = str_replace(':mainscenario:', '', $page);
					$page = str_replace(':alternativescenario:', '', $page);
					$page = str_replace(':message:', '<p class="message success"><span lang="en">Use Case</span> inserito!</p>', $page);
				}
				else {
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
					$page = str_replace(':message:', '<p class="message warning">Riempire tutti i campi!</p>', $page);
				}
			} else {
				$page = str_replace(':name:', '', $page);
				$page = str_replace(':description:', '', $page);
				$page = str_replace(':precondition:', '', $page);
				$page = str_replace(':postcondition:', '', $page);
				$page = str_replace(':mainscenario:', '', $page);
				$page = str_replace(':alternativescenario:', '', $page);
				$page = str_replace(':message:', '', $page);
			}
			return $page;
		}
		//Metodo che si occupa di fare il rendering del form del tracciamento con le informazioni dinamiche
		public function adjustTrackingForm($page, $data, $projectid) {
			$rs = $this->select($projectid);
			$str = "";
			if($rs) {
				foreach($rs as $uc)
					$str .= '<option value="'.$uc['id'].'">'.$uc['usecaseid'].'-'.$uc['name'].'</option>';
				$page = str_replace(":usecaseoptions:", $str, $page);
			} else
				$page = str_replace(":usecaseoptions:", "", $page);
			$rs = $this->selectRequirements($projectid);
			$str = '<div class="multiple">';
			$i = 0;
			if($rs) {
				foreach($rs as $r) {
					$str .= '<input id="checkbox'.$i.'" type="checkbox" name="requirement[]" value="'.$r["id"].'" />';
					$str .= '<label for="checkbox'.$i.'">'.$r["requirementid"].' - '.$r["name"].'</label>';
					++$i;
				}
				$str .= '</div>';
				$page = str_replace(":requirementoptions:", $str, $page);
			} else
				$page = str_replace(":requirementoptions:", "<a href='insertRequirement.php'>Inserisci</a> un requisito se vuoi effettuare il tracciamento.", $page);
			if($data)
				if($data == 'insert')
					$page = str_replace(":message:", '<p class="message success">Tracciamento inserito!</p>', $page);
				else
					$page = str_replace(":message:", '<p class="message warning">Inserisci almeno un requisito!</p>', $page);
			else
				$page = str_replace(":message:", '', $page);
			return $page;
		}
		//Metodo che si occupa di tracciare uno usecase con i suoi requisiti
		public function track($obj) {
			$this->query("DELETE FROM usecaserequirements WHERE usecaseid = {$obj["usecase"]};");
			$this->resultSet();
			foreach($obj["requirement"] as $requirement) {
				$this->query("INSERT INTO usecaserequirements VALUES ({$obj["usecase"]}, {$requirement});");
				$this->resultSet();
			}
		}
		//Metodo che ritorna il tracciamento tra gli usecase e i vari requisiti
		public function selectTracking($projectid) {
			$this->query("SELECT id, usecaseid, name FROM usecase WHERE projectid = {$projectid};");
			$rs = $this->resultSet();
			if($rs) {
				$array = array();
				foreach($rs as $uc) {
					$str = "";
					$this->query("SELECT * FROM usecaserequirements, requirements WHERE usecaserequirements.requirementid = requirements.id AND usecaseid = {$uc['id']};");
					$rsTracking = $this->resultSet();
					foreach($rsTracking as $t) {
						$str .= $t["requirementid"].' ';
					}
					if($str != "")
						$array[$uc['usecaseid']] = array($str, $uc['id'], $uc["name"]);
				}
				return $array;
			} else
				return array();
		}
		//Metodo che ritorna i requisiti tracciati con lo usecase corrente
		public function getTracking($id) {
			$this->query("SELECT id, requirements.requirementid as requirementid, name FROM requirements, usecaserequirements WHERE requirements.id = usecaserequirements.requirementid AND usecaseid = {$id} ORDER BY requirementid;");
			return $this->resultSet();
		}
		//Metodo che elimina il tracciamento dello usecase corrente
		public function deleteTracking($id) {
			$this->query("DELETE FROM usecaserequirements WHERE usecaseid = {$id};");
			return $this->resultSet();
		}
		//Metodo che ritorna il valore se $value � presente in $array, altrimenti ritorna false
		public function find($value, $array) {
			$found = false;
			for($i = 0; $i < count($array) && !$found; ++$i)
				if($array[$i] == $value)
					$found = true;
			return $found;
		}
		//Metodo che riempie il form con le informazioni dello usecase
		public function fillForm($page, $data, $projectid) {
			$page = str_replace(':usecaseid:', $data["usecaseid"], $page);
			$page = str_replace(':name:', $data["name"], $page);
			$page = str_replace(':description:', $data["description"], $page);
			$page = str_replace(':precondition:', $data["precondition"], $page);
			$page = str_replace(':postcondition:', $data["postcondition"], $page);
			$page = str_replace(':mainscenario:', $data["mainscenario"], $page);
			$page = str_replace(':alternativescenario:', $data["alternativescenario"], $page);
			$rs = $this->select($projectid);
			$str = "";
			if($rs) {

				$rs_hierarchy = $this->getHierarchy($data["id"]);
				$hierarchy = [];
				foreach($rs_hierarchy as $uc)
					array_push($hierarchy, $uc["id"]);

				foreach($rs as $uc)
					if($uc["id"] == $data["parent"])
						$str .= '<option value="'.$uc['id'].'" selected="selected">'.$uc['usecaseid'].'-'.$uc['name'].'</option>';
					else if($uc["id"] != $data["id"] && !in_array($uc["id"], $hierarchy))
						$str .= '<option value="'.$uc['id'].'">'.$uc['usecaseid'].'-'.$uc['name'].'</option>';
				$page = str_replace(":parentoptions:", $str, $page);
			} else
				$page = str_replace(":parentoptions:", "", $page);
			$rs = $this->getMyGeneralizations($data["id"]);
			$str = '<div class="multiple">';
			$str .= '<input id="checkbox0" type="checkbox" name="generalization[]" value="NULL" checked="checked" />';
			$str .= '<label for="checkbox0">Nessuno</label>';
			$i = 1;
			$array = array();
			foreach($rs as $generalization)
				$array[] = $generalization["generalizationusecaseid"];
			$rs = $this->select($projectid);
			if($rs) {
				foreach($rs as $uc) {
					if($this->find($uc["id"], $array)) {
						$str .= '<input id="checkbox'.$i.'" type="checkbox" name="generalization[]" value="'.$uc["id"].'" checked="checked" />';
						$str .= '<label for="checkbox'.$i.'">'.$uc["usecaseid"].' - '.$uc["name"].'</label>';
					} else {
						$str .= '<input id="checkbox'.$i.'" type="checkbox" name="generalization[]" value="'.$uc["id"].'" />';
						$str .= '<label for="checkbox'.$i.'">'.$uc["usecaseid"].' - '.$uc["name"].'</label>';
					}
					++$i;
				}
				$str .= "</div>";
				$page = str_replace(":generalizationoptions:", $str, $page);
			} else
				$page = str_replace(":generalizationoptions:", "", $page);
			$rs = $this->getMyInclusions($data["id"]);
			$str = '<div class="multiple">';
			$str .= '<input id="checkbox0" type="checkbox" name="inclusion[]" value="NULL" checked="checked" />';
			$str .= '<label for="checkbox0">Nessuno</label>';
			$i = 1;
			$array = array();
			foreach($rs as $inclusion)
				$array[] = $inclusion["includedusecaseid"];
			$rs = $this->select($projectid);
			if($rs) {
				foreach($rs as $uc) {
					if($this->find($uc["id"], $array)) {
						$str .= '<input id="checkbox'.$i.'" type="checkbox" name="inclusion[]" value="'.$uc["id"].'" checked="checked" />';
						$str .= '<label for="checkbox'.$i.'">'.$uc["usecaseid"].' - '.$uc["name"].'</label>';
					} else {
						$str .= '<input id="checkbox'.$i.'" type="checkbox" name="inclusion[]" value="'.$uc["id"].'" />';
						$str .= '<label for="checkbox'.$i.'">'.$uc["usecaseid"].' - '.$uc["name"].'</label>';
					}
					++$i;
				}
				$str .= "</div>";
				$page = str_replace(":inclusionoptions:", $str, $page);
			} else
				$page = str_replace(":inclusionoptions:", "", $page);
			$rs = $this->getMyExtensions($data["id"]);
			$str = '<div class="multiple">';
			$str .= '<input id="checkbox{$i}" type="checkbox" name="extension[]" value="NULL" checked="checked" />';
			$str .= '<label for="checkbox{$i}">Nessuno</label>';
			$i++;
			$array = array();
			foreach($rs as $extension)
				$array[] = $extension["extendedusecaseid"];
			$rs = $this->select($projectid);
			if($rs) {
				foreach($rs as $uc) {
					if($this->find($uc["id"], $array)) {
						$str .= '<input id="checkbox"'.$i.'" type="checkbox" name="extension[]" value="'.$uc["id"].'" checked="checked" />';
						$str .= '<label for="checkbox'.$i.'">'.$uc["usecaseid"].' - '.$uc["name"].'</label>';
					} else {
						$str .= '<input id="checkbox'.$i.'" type="checkbox" name="extension[]" value="'.$uc["id"].'" />';
						$str .= '<label for="checkbox'.$i.'">'.$uc["usecaseid"].' - '.$uc["name"].'</label>';
					}
					++$i;
				}
				$str .= "</div>";
				$page = str_replace(":extensionoptions:", $str, $page);
			} else
				$page = str_replace(":extensionoptions:", "", $page);
			$rs = $this->getActors($data["id"]);
			$str = '<div class="multiple">';
			$str .= '<input id="checkbox{$i}" type="checkbox" name="actor[]" value="NULL" checked="checked" />';
			$str .= '<label for="checkbox{$i}">Nessuno</label>';
			++$i;
			$array = array();
			foreach($rs as $actor)
				$array[] = $actor["actorsid"];
			$rs = $this->selectActors($projectid);
			if($rs) {
				foreach($rs as $actor) {
					if($this->find($actor["id"], $array)) {
						$str .= '<input id="checkbox'.$i.'" type="checkbox" name="actor[]" value="'.$actor["id"].'" checked="checked" />';
						$str .= '<label for="checkbox'.$i.'">'.$actor["name"].'</label>';
					} else {
						$str .= '<input id="checkbox'.$i.'" type="checkbox" name="actor[]" value="'.$actor["id"].'" />';
						$str .= '<label for="checkbox'.$i.'">'.$actor["name"].'</label>';
					}
					++$i;
				}
				$str .= "</div>";
				$page = str_replace(":actoroptions:", $str, $page);
			} else
				$page = str_replace(":actoroptions:", "", $page);
			return $page;
		}
		//Metodo che riempie il form del tracciamento con le informazioni degli usecase e dei requisiti
		public function fillTrackingForm($page, $data, $projectid) {
			$page = str_replace(':id:', $data["id"], $page);
			$page = str_replace(':usecaseid:', $data["usecaseid"], $page);
			$page = str_replace(':name:', $data["name"], $page);
			$rs = $this->getTracking($data["id"]);
			$str = '<div class="multiple">';
			$i = 0;
			$array = array();
			foreach($rs as $t)
				$array[] = $t["id"];
			$rs = $this->selectRequirements($projectid);
			if($rs) {
				foreach($rs as $requirement) {
					if($this->find($requirement['id'], $array)) {
						$str .= '<input id="checkbox'.$i.'" type="checkbox" name="requirement[]" value="'.$requirement["id"].'" checked="checked" />';
						$str .= '<label for="checkbox'.$i.'">'.$requirement["requirementid"].' - '.$requirement["name"].'</label>';
					} else {
						$str .= '<input id="checkbox'.$i.'" type="checkbox" name="requirement[]" value="'.$requirement["id"].'" />';
						$str .= '<label for="checkbox'.$i.'">'.$requirement["requirementid"].' - '.$requirement["name"].'</label>';
					}
					++$i;
				}
				$str .= "</div>";
				$page = str_replace(":requirementoptions:", $str, $page);
			} else
				$page = str_replace(":requirementoptions:", "", $page);
			return $page;
		}
	}
?>

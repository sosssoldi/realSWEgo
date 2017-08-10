<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	session_start();
	include_once "php/Database/Database.php";
	include_once "php/DAO/UsecaseDAO.php";
	include_once "php/DAO/RequirementDAO.php";

	if(empty($_SESSION)) {
		header("Location: index.html");
		exit();
	}

	if(array_key_exists("type", $_SESSION) && $_SESSION["type"] == "admin") {
		header("Location: admin.php");
		exit();
	}

	//Creating groupname's folders
	$basedir = 'plantUML/'.$_SESSION["groupname"].$_SESSION["id"];
	create_folder($basedir);
	create_folder($basedir."/Usecase");
	create_folder($basedir."/Plantuml");
	create_folder($basedir."/Requirement");
	create_folder($basedir."/Tracking");
	//Creating usecase file
	create_usecase_file($basedir."/Usecase");
	//Creating requirement file
	create_requirement_file($basedir."/Requirement");
	//Creating tracking file
	create_tracking_file($basedir."/Tracking");
	//Creating plantuml file
	create_plantuml_file($basedir."/Plantuml");
	//Zip groupname-s folder
	zip($basedir.".zip", $basedir);
	//Return content
	download($basedir.".zip", $_SESSION["groupname"].$_SESSION["id"].".zip");
?>

<?php
function create_folder($path) {
	$oldmask = umask(0);
	if(is_dir($path)) {
		delete_folder($path);
		rmdir($path);
	}
	$done = mkdir($path, 0777, true);
	umask($oldmask);
	//echo $done? $path." created" : $path." not created";
	return $done;
}

function delete_folder($path) {
	if(file_exists($path."/Usecase/usecase.tex"))
		unlink($path."/Usecase/usecase.tex");
	if(file_exists($path."/Requirement/requirement.tex"))
		unlink($path."/Requirement/requirement.tex");
	if(file_exists($path."/Tracking/tracking.tex"));
		unlink($path."/Tracking/tracking.tex");
	$files = scandir($path."/Plantuml");
	foreach($files as $file) {
		if($file != "." && $file != "..")
			unlink($path."/Plantuml/".$file);
	}
	rmdir($path."/Usecase");
	rmdir($path."/Requirement");
	rmdir($path."/Tracking");
	rmdir($path."/Plantuml");
}

function create_usecase_file($path) {
	$usecasef = fopen($path."/usecase.tex","w");
	$usecaseDAO = new UsecaseDAO();
	$rs = $usecaseDAO->select($_SESSION["id"]);
	$db = new Database();
	foreach($rs as $usecase) {
		$title = "\subsection{Caso d'uso \hypertarget{{$usecase['usecaseid']}}{{$usecase['usecaseid']}}: {$usecase['name']}}";
		$actor = "\item \\textbf{Attori}: ".getActor($usecase["id"]);
		$description = "\item \\textbf{Descrizione}: ".$usecase["description"];
		$pre = "\item \\textbf{Precondizione}: ".$usecase["precondition"];
		$mainscenario = "\item \\textbf{Flusso principale degli eventi}: ".$usecase["mainscenario"]."\n";
		$image = "";
		$db->query("SELECT * FROM usecase WHERE parent = {$usecase["id"]} ORDER BY usecaseid;");
		$sons = $db->resultSet();
		if($sons) {
			$image = "\begin{figure} [H]\n";
			$image .= "\centering\n";
			$imagetitle = str_replace(".", "-", $usecase["usecaseid"]);
			$image .= "\includegraphics[scale=0.45]{./{$imagetitle}.png}\n";
			$image .= "\caption{{$usecase["name"]}}\label{}\n";
			$image .= "\\end{figure}\n";
			$mainscenario .= "\begin{itemize}\n";
			foreach($sons as $son) {
				$mainscenario .= "\item ".$son["name"]." (".$son["usecaseid"].")\n";
			}
			$mainscenario .= "\\end{itemize}\n";
		}
		$alternativescenario = "";
		if($usecase["alternativescenario"] != "")
			$alternativescenario = "\item \\textbf{Scenari alternativi}: ".$usecase["alternativescenario"]."\n";
		$post = "\item \\textbf{Postcondizione}: ".$usecase["postcondition"];
		$myincl = $usecaseDAO->getMyInclusionsInfo($usecase["id"]);
		$inclusion = "";
		if($myincl) {
			$inclusion = "\item \\textbf{Inclusioni}:\n";
			$inclusion .= "\begin{itemize}\n";
			foreach($myincl as $incl) {
				$inclusion .= "\item ".$incl["name"]." (".$incl["usecaseid"].")\n";
			}
			$inclusion .= "\\end{itemize}\n";
		}
		$myext = $usecaseDAO->getMyExtensionsInfo($usecase["id"]);
		$extension = "";
		if($myext) {
			$extension = "\item \\textbf{Estensioni}:\n";
			$extension .= "\begin{itemize}\n";
			foreach($myext as $ext) {
				$extension .= "\item ".$ext["name"]." (".$ext["usecaseid"].")\n";
			}
			$extension .= "\\end{itemize}\n";
		}
		fputs($usecasef, $title."\n");
		fputs($usecasef, $image);
		fputs($usecasef, "\begin{itemize}\n");
		fputs($usecasef, $actor."\n");
		fputs($usecasef, $description."\n");
		fputs($usecasef, $pre."\n");
		fputs($usecasef, $mainscenario);
		fputs($usecasef, $alternativescenario);
		fputs($usecasef, $post."\n");
		fputs($usecasef, $inclusion);
		fputs($usecasef, $extension);
		fputs($usecasef, "\\end{itemize}\n");
	}
	fclose($usecasef);
}

function getActor($id) {
	$db = new Database();
	$db->query("SELECT name FROM usecaseactors,actors WHERE usecaseactors.actorsid=actors.id AND usecaseid={$id};");
	$rs = $db->resultSet();
	$result = "";
	foreach($rs as $actor) {
		$result .= $actor["name"].", ";
	}
	$result = substr($result, 0, -2);
	return $result;
}

function create_requirement_file($path) {
	$requirementf = fopen($path."/requirement.tex","w");
	$t = array("F","Q","P","V");
	$tdesc = array("Funzionali", "Di Qualità", "Prestazionali", "Di Vincolo");
	$i = 0;
	$db = new Database();
	while($i < count($t)) {
		$db->query("SELECT requirements.id as id, requirementid, requirements.description as description, sources.name as name FROM requirements, sources WHERE type LIKE '%{$t[$i]}%' AND requirements.source = sources.id AND requirements.projectid = {$_SESSION["id"]} ORDER BY requirementid ASC;");
		$rs = $db->resultSet();
		if($rs) {
			fputs($requirementf, "\subsection{Requisiti {$tdesc{$i}}}\n");
			fputs($requirementf, "\\normalsize\n");
			fputs($requirementf, "\\begin{longtable}{|c|>{\\centering}m{7cm}|c|}\n");
			fputs($requirementf, "\\hline\n");
			fputs($requirementf, "\\textbf{Id Requisito} & \\textbf{Descrizione} & \\textbf{Fonte}\\\\\n");
			fputs($requirementf, "\\hline\n");
			fputs($requirementf, "\\endhead\n");
			foreach($rs as $r) {
				fputs($requirementf, "\\hypertarget{{$r['requirementid']}}{{$r['requirementid']}} & {$r['description']} & {$r["name"]} \\\\ \\hline \n");
			}
			fputs($requirementf, "\\caption[Requisiti {$tdesc{$i}}]{Requisiti {$tdesc{$i}}\n");
			fputs($requirementf, "\\label{tabella:req{$i}}\n");
			fputs($requirementf, "\\end{longtable}\n");
			fputs($requirementf, "\\clearpage\n");
		}
		$i++;
	}
	fclose($requirementf);
}

function create_tracking_file($path) {
	$trackingf = fopen($path."/tracking.tex","w");
	$requirementDAO = new RequirementDAO();
	$usecaseDAO = new UsecaseDAO();
	$db = new Database();
	if($requirementDAO->selectTrackedRequirements($_SESSION["id"])) {
		//Table Requirements-Use Case
		fputs($trackingf, "\\normalsize\n");
		fputs($trackingf, "\\begin{longtable}{|c|c|}\n");
		fputs($trackingf, "\\hline\n");
		fputs($trackingf, "\\textbf{Codice Requisiti} & \\textbf{Codice Use case} \\\\\n");
		fputs($trackingf, "\\hline\n");
		fputs($trackingf, "\\endhead\n");
		$rs = $requirementDAO->select($_SESSION["id"]);
		foreach($rs as $r) {
			$db->query("SELECT usecase.usecaseid as usecaseid FROM usecaserequirements, usecase WHERE usecaserequirements.usecaseid=usecase.id AND usecaserequirements.requirementid={$r['id']} ORDER BY usecase.usecaseid ASC;");
			$usecases = $db->resultSet();
			if($usecases) {
				$str = "\hyperlink{{$r["requirementid"]}}{{$r["requirementid"]}} ";
				foreach($usecases as $usecase) {
					$str .= "& \hyperlink{{$usecase['usecaseid']}}{{$usecase['usecaseid']}}\\\\\n"; 
				}
				$str .= "\\hline\n";
				fputs($trackingf, $str);
			}
		}
		fputs($trackingf, "\\caption[Tracciamento Requisiti-Use case]{Tracciamento Requisiti-Use case}\n");
		fputs($trackingf, "\\label{tabella:requi-usecase}\n");
		fputs($trackingf, "\\end{longtable}\n");
		fputs($trackingf, "\\clearpage\n");
		//Table Use Case-Requirements
		fputs($trackingf, "\\normalsize\n");
		fputs($trackingf, "\\begin{longtable}{|c|c|}\n");
		fputs($trackingf, "\\hline\n");
		fputs($trackingf, "\\textbf{Codice Use case} & \\textbf{Codice Requisiti} \\\\\n");
		fputs($trackingf, "\\hline\n");
		fputs($trackingf, "\\endhead\n");
		$rs = $usecaseDAO->select($_SESSION["id"]);
		foreach($rs as $usecase) {
			$requirements = $usecaseDAO->getTracking($usecase["id"]);
			if($requirements) {
				$str = "\hyperlink{{$usecase["usecaseid"]}}{{$usecase["usecaseid"]}} ";
				foreach($requirements as $requirement) {
					$str .= "& \hyperlink{{$requirement['requirementid']}}{{$requirement['requirementid']}}\\\\\n"; 
				}
				$str .= "\\hline\n";
				fputs($trackingf, $str);
			}
		}
		fputs($trackingf, "\\caption[Tracciamento Use case-Requisiti]{Tracciamento Use case-Requisiti}\n");
		fputs($trackingf, "\\label{tabella:requi-usecase}\n");
		fputs($trackingf, "\\end{longtable}\n");
		fputs($trackingf, "\\clearpage\n");
	}
	fclose($trackingf);
}

function create_plantuml_file($path) {
	$db = new Database();
	$db->query("SELECT DISTINCT parent FROM usecase WHERE parent IS NOT NULL");
	$rs = $db->resultSet();
	foreach($rs as $parent) {
		$db->query("SELECT * FROM usecase WHERE id = {$parent["parent"]};");
		$info = $db->resultSet();
		if($info) {
			$info = $info[0];
			$filename = str_replace('.', '-', $info["usecaseid"]).".txt";
			$file = fopen($path."/".$filename, "w");
			fputs($file, "@startuml\n");
			fputs($file, "left to right direction\n");
			fputs($file, "skinparam packageStyle rectangle\n");
			$db->query("SELECT name FROM usecaseactors, actors WHERE usecaseactors.actorsid = actors.id AND usecaseactors.usecaseid = {$info["id"]} ORDER BY name;");
			$actors = $db->resultSet();
			if($actors) {
				foreach($actors as $actor) {
					$name = str_replace(" ", "-", $actor["name"]);
					fputs($file, "actor ".$name."\n");
				}
			}
			fputs($file,"rectangle ".$info["usecaseid"]." {\n");
			$link = "--";
			if($info["generalization"]) {
				$link = "<|--";
			}
			$db->query("SELECT usecase.usecaseid as ucid, usecase.name as ucname, actors.name as aname FROM usecase, usecaseactors, actors WHERE usecase.id = usecaseactors.usecaseid AND usecaseactors.actorsid = actors.id AND parent = {$info["id"]} ORDER BY aname, ucid;");
			$sons = $db->resultSet();
			if($sons) {
				foreach($sons as $son) {
					$name = str_replace(" ", "-", $son["aname"]);
					fputs($file,"{$name} {$link} (".$son["ucid"]." - ".$son["ucname"].")\n");
				}
			}
			fputs($file, "}\n");
			fputs($file, "@enduml");
			fclose($file);
		}
	}
}

function zip($name, $path) {
	$zip = new ZipArchive();
	$zip->open($name, ZipArchive::CREATE);
    if(file_exists($path."/Usecase/usecase.tex"))
    	$zip->addFile($path."/Usecase/usecase.tex");
    if(file_exists($path."/Requirement/requirement.tex"))
    	$zip->addFile($path."/Requirement/requirement.tex");
    if(file_exists($path."/Tracking/tracking.tex"))
    	$zip->addFile($path."/Tracking/tracking.tex");
    $files = scandir($path."/Plantuml");
    foreach($files as $file) {
    	if($file != "." && $file != "..")
    		$zip->addFile($path."/Plantuml/".$file);
    }
    $zip->close();
}

function download($path, $name) {
    header('Content-Type: application/force-download');
    header("Content-Disposition: attachment; filename='{$name}'");
    header('Content-Length: '.filesize($path));
    header("Location: {$path}");
}
?>
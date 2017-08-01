<?php
	session_start();
	include_once "autoloader.php";

	use \php\DAO;
	use \php\Database;
	use \php\Object;

	/*if(empty($_SESSION))
		header("Location: index.html");*/

	if(empty($_GET) || !array_key_exists("obj", $_GET))
		header("Location: home.html");

	if(empty($_POST)) {
		$page = file_get_contents("template/insert.html");
		$form = file_get_contents("template/{$_GET["obj"]}Form.html");
		$page = str_replace(":form:",$form, $page);
		$DaoClass = $_GET['obj'].'DAO';
		$obj = new $DaoClass(null);
		echo $obj->adjustForm($page);
	} else {
		$Class = $_GET['obj'];
		$DaoClass = $Class.'DAO';

		$object = new $Class($_GET);
		$objectDAO = new $DaoClass($object);

		//echo file_get_contents('insert'.$Class.'.html');
		//$result = $objectDao->insert();
		//echo $result? $object->renderForm() : $object->renderFilledForm();
	}
?>
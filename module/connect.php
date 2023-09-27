<?php
//02-03 谷上
if(isset($_SESSION["db_errmsg"])){
	$_SESSION["db_errmsg"]=null;
}

include("config.php");		//config.phpファイル読み込み
include($config_path . $config_file); 		//config.phpにある変数を使い"../tools/k2_config.php"をinclude

try {
	$db = new PDO($database_dsn,$database_user,$database_pass);
	$db->exec("SET NAMES utf8");
	$db->setAttribute(PDO::ATTR_CASE,PDO::CASE_LOWER);
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	
}catch (PDOException $e) {
	$db=NULL;
}

function db_execution($sql,$arr) {
	global $db;
	try{
		// SQL実行
		$result = $db->prepare($sql);
		$result->execute($arr);
		
		return $result;
	}catch( PDOException $e ) {
		return FALSE;		
	}
}

function db_transaction($sql,$arr) {
	global $db;
	try{
		// SQL実行
		$result = $db->prepare($sql);
		$result->execute($arr);
		
		return $result;
	}catch( PDOException $e ) {
		$db->rollBack();
		return false;
	}
}
?>
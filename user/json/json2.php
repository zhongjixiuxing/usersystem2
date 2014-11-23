<?php
	
   	$temp_path = $_SERVER['DOCUMENT_ROOT']."/UserSystem2"; //网站根目录路径
	require_once($temp_path."/global.php");               //请求网站的全局配置路径；
	require_once(ROOT_PATH."/user/php/model/User.php");  //ROOT_PATH是global.php中定义的一个路径
	require_once ROOT_PATH.'/global/uuid/utils.php';
	require_once ROOT_PATH.'/dbutils/MongoDBUtil.php';
	
	
	$method = $_REQUEST['method'];
   	$m = MongoDBUtil::_getIntance();
   	$db = $m->$db_name;
   	$collection = $db->createCollection($table_name);
   
   	if($method === 'getInfo'){
   		$result = MongoDBUtil::getAllRecordJsonStr_Dynamic($collection);
   		echo $result;
   	}elseif	($method === 'delete'){
   		$json = $_POST['json'];
   		$jsonObjs = json_decode($json,true);
   		$ids = MongoDBUtil::deleteRow($jsonObjs,$collection);
   		$res = json_encode($ids);
   		echo $res;
   	}elseif ($method === 'add'){
   		$json = $_POST['json'];
   		$jsonObjs = json_decode($json, true);
		$result = json_encode(MongoDBUtil::addRows($jsonObjs, $collection));
   		echo $result;
   	}elseif ($method === 'update'){
   		$json = $_POST['json'];
   		$jsonObjs = json_decode($json, true);
   		MongoDBUtil::updateRows($jsonObjs, $collection);
   		echo "update ok!";
   	}elseif ($method === 'test'){
   		test();
   		
   	}
   	
   	function test($jsonObjs){
   		$m = MongoDBUtil::_getIntance();
   		$db = $m->user;
   		$collection = $db->createCollection("mycol");
   		$json = MongoDBUtil::addRows($jsonObjs, $collection);
		return $json;
   	}
   
?>
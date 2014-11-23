<?php
	/**
	 * 用户登录处理
	 * 		《这个后台的数据库使用到MongoDB》
	 */

	$temp_path = $_SERVER['DOCUMENT_ROOT']."/UserSystem2"; //网站根目录路径
	require_once($temp_path."/global.php");               //请求网站的全局配置路径；
	require_once(ROOT_PATH."/user/php/model/User.php");  //ROOT_PATH是global.php中定义的一个路径

	
	$name = $_GET['name'];
	$value = $_GET['value'];
	echo "name : ".$name."<br>";
	echo "pw : ".$value."<br>";
	
	$m = new MongoClient("192.168.16.54");
	$db = $m->TestForHang;
	$collection = $db->createCollection("user");
	
	addUser($name, $value, $collection);
//   	addUser($name, $value, $collection);
//	echo "增加成功";
	
//	removeUserByName($name, $collection);
//	echo "移除成功";
	
//	updateName($name, $value, $collection);
//  echo "更新成功";
   	
//	findUser($name, $collection);
//	echo "所用用户的信息";
	
   /**
    * 增加一个User到数据库中
    * Enter description here ...
    * @param User $user
    * @param unknown_type $collection
    */
   function addUser($name, $value, $collection){
   		$document  = array(
   			"name" => $name,
   			"value" => $value,
   		);
   		$collection->insert($document);
   }
   
   /**
    * update
    * Enter description here ...
    * @param $oldName
    * @param $newName
    * @param $collection
    */
   function updateName($oldName, $newName, $collection){
   		$newdata = array('$set' => array("name" => $newName));
		$collection->update(array("name" => $oldName), $newdata);
   }
   
   /**
    * find
    * Enter description here ...
    */
   function findUser($name, $collection){
   		$query = array(
   			"name" => $name
   		);
   		
   		$cursor = $collection->find($query);
   		
  		foreach ($cursor as $document) {
  			echo $document["_id"] . "\n";
  			echo $document["name"] . "\n";
  			echo $document["value"] . "\n";
//	      	echo print_r($document) . "\n";
	   }
//		var_dump($cursor);
   }
   
   /**
    * 根据用户名删除用户的信息；
    * Enter description here ...
    * @param $name
    * @param $collection
    */
   function removeUserByName($name, $collection){
   		$query = array(
   			"name" => $name
   		);
   		
   		$collection->remove($query);
   }
   

?>
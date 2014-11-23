<?php
	
   	$temp_path = $_SERVER['DOCUMENT_ROOT']."/UserSystem2"; //网站根目录路径
	require_once($temp_path."/global.php");               //请求网站的全局配置路径；
	require_once(ROOT_PATH."/user/php/model/User.php");  //ROOT_PATH是global.php中定义的一个路径
	require_once ROOT_PATH.'/global/uuid/utils.php';
	require_once ROOT_PATH.'/dbutils/MongoDBUtil.php';
	
	
	$method = $_REQUEST['method'];
   	$m = new MongoClient("localhost");
   	$db = $m->user;
   	$collection = $db->createCollection("mycol");
   
   	if($method === 'getInfo'){
   		$result = getAllRecordJsonStr_Dynamic($collection);
   		echo $result;
   	}elseif	($method === 'delete'){
   		$json = $_POST['json'];
   		$jsonObjs = json_decode($json,true);
   		$ids = deleteRow($jsonObjs,$collection);
   		$res = json_encode($ids);
   		echo $res;
   	}elseif ($method === 'add'){
   		$json = $_POST['json'];
   		$jsonObjs = json_decode($json, true);
		$result = json_encode(addRows_Dynamic($jsonObjs, $collection));
   		echo $result;
   	}elseif ($method === 'update'){
   		$json = $_POST['json'];
   		$jsonObjs = json_decode($json, true);
   		updateRows($jsonObjs, $collection);
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
   
   	/**
   	 * 更新多个纪录
   	 * 	需要提交要更新的纪录集合，每个纪录必须包含id字段
   	 * Enter description here ...
   	 * @param $jsonObjs
   	 * @param $collection
   	 */
   	function updateRows($jsonObjs, $collection){
   		for($x=0; $x<count($jsonObjs); $x++){
   			$keys = array_keys($jsonObjs[$x]);
   			//$newdata = array('$set' => array("city" => "huazhou","country" => "us"));
   			$newdata = array();
   			for($i=0; $i<count($keys); $i++){
   				if($keys[$i] != "id"){
   					$newdata[$keys[$i]] = $jsonObjs[$x][$keys[$i]];
   				}else{
   					$id = $jsonObjs[$x][$keys[$i]];
   				} 
   			}
   			$arr = array('$set' => $newdata);  //如果没有$set,后果.....爱爱爱 忘了有什么后果了，严重吧
   			
   			if(strlen($id) > 30){
   				$result = $collection->update(array("_id" => $jsonObjs[$x]['id']), $arr);
   			}else{
   				$result = $collection->update(array("_id" => new MongoId($id)), $arr);
   			}
   			
	   		
	   		echo "result : ".print_r($result)."\n";   			
   		}
   	}
   	
   	/**
   	 * 动态增加数据
   	 * 	这里得_id是自定义的UUID值，并且所有的操作的id就是_id,两者是相同
   	 * 	返回值是函数刚插入id（UUID）
   	 * Enter description here ...
   	 * @param $jsonObjs
   	 * @param $collection
   	 */
   	function addRows_Dynamic($jsonObjs, $collection){
   		$result = array();
	   	for($x=0; $x<count($jsonObjs); $x++){
   			$uuid = UUIDUtils::guid();
   			$keys = array_keys($jsonObjs[$x]);
   			$document  = array();
   			for($y=0; $y<count($keys); $y++){
   				if($keys[$y] != "id"){
   					$document[$keys[$y]] = $jsonObjs[$x][$keys[$y]];
   				} 
   			}
			$document["_id"] = $uuid;
   			$collection->insert($document);
   			$result[$jsonObjs[$x]["id"]] = $uuid;
   		}
   		return $result;
   	}
   	
   	/**
   	 * 将用户的信息保存到数据库
   	 * 	注意： 此函数太死板，已经作废，请使用addRows_Dynamic() 函数保存数据
   	 * Enter description here ...
   	 * @param $jsonObjs
   	 * @param $collection
   	 */
   	function addRows($jsonObjs, $collection){
   		$result = array();
   		
   		for($x=0; $x<count($jsonObjs); $x++){
   			$uuid = UUIDUtils::guid();
   			$document  = array(
   			"name" => $jsonObjs[$x]["name"],
   			"city" => $jsonObjs[$x]["city"],
   			"country" => $jsonObjs[$x]["country"],
   			"_id" => $uuid,
   		);
   			$collection->insert($document);
   			$result[$jsonObjs[$x]["id"]] = $uuid;
   		}
   		
   		return $result;
   	}
   	
   	/**
   	 * 根据用户的Id数据删除用户的数据
   	 * 		返回成功删除的id数组
   	 * Enter description here ...
   	 * @param unknown_type $jsonObjs
   	 * @param unknown_type $collection
   	 */
   	function deleteRow($jsonObjs, $collection){
   		$ids = array();
   		for($x=0; $x<count($jsonObjs); $x++){
			$id = $jsonObjs[$x]["id"];
			if(strlen($id) > 30){
				$cursor = $collection->remove(array("_id" => $id));
			}else{
				$del = new MongoId($id);
   				$cursor = $collection->remove(array("_id" => $del));
			}
			
   			if($cursor['ok'] == 1){
   				$ids[count($ids)] = $id;
   			}
   		}
   		return $ids;
   	}
   	
   	/**
   	 * 获取表中所有的数据，并将其转换成json字符串返回，
   	 * 		注意这里的json 是根据数据库中字段名动态生成的
   	 * 			处理_id 主键的外
   	 * Enter description here ...
   	 */
   	function getAllRecordJsonStr_Dynamic($collection){
   		$cursor = $collection->find();
	   	$json = '[';
	   	// 迭代显示文档标题
	   	//$size = count($cursor);// 这种方式无效
	   	$size = count(iterator_to_array($cursor));
	   	$num = 0;
	   	
		foreach ($cursor as $document) {
			$json .="{";
			$keys = array_keys($document);
			$len = count($keys);
			$json .="\"id\":\"".$document['_id']."\",";
			for ($x=0; $x<$len; $x++){
				$key = $keys[$x];
				if($key !== "_id"){
					$json .="\"".$key."\":\"".$document[$key]."\"";
					if ($x != $len-1){
						$json .= ",";
					}
				}
			}
			$json .="}";
			
			if($num < $size-1){
				$json .=",";
			}
			$num++;
		}
		$json .= ']';
		return $json;
		
   	}
   	
   /**
    * 获取user2表中所有的数据，并转换成json字符串返回 
    * 	注意： 这种方式太死板了，因此此函数已经被废弃了
    * Enter description here ...
    * @param $co
    */
   function getAllRecordJsonStr($collection){
   	$cursor = $collection->find();
   	$json = '[';
	
   	// 迭代显示文档标题
	foreach ($cursor as $document) {
		$json .="{";
		$json .="\"id\":\"".$document['_id']."\",";
		$json .="\"name\":\"".$document['name']."\",";
		$json .="\"city\":\"".$document['city']."\",";
		$json .="\"country\":\"".$document['country']."\"";
		$json .="}";
		$json .=",";
	}
	$json = substr($json, 0, strlen($json)-1);
	
	$json .= ']';
	
	return $json;
   }
   
   /**
    * 增加一个User到数据库中
    * Enter description here ...
    * @param User $user
    * @param unknown_type $collection
    */
   function addUser(User $u, $collection){
   		echo "name : ".$u->name."<br>";
   		$document  = array(
   			"name" => $u->name,
   			"email" => $u->email,
   			"pw" => $u->pw
   		);
   		
   		$collection->insert($document);
   }
   
   /**
    * 根据Id删除一个User的信息
    * Enter description here ...
    * @param $id
    */
   function deleteUser($id){
	   $cursor = $collection->remove();
   }
   
   /**
    * 根据用户名获取用户的数据
    * Enter description here ...
    */
   function getUserByName($name, $collection){
		echo "oks".$collection."<br>";
//   		$query = array("id" => 1);
//   		$cursor = $collection->find($query);
   		$cursor = $collection->find();
   		
	   // 迭代显示文档标题
	   foreach ($cursor as $document) {
	      echo $document["name"] . "\n";
	   }
   }
   
   /**
    * 更新用户名，要求必须提供用户的Id在$user变量中
    * Enter description here ...
    * @param $user
    */
   function updateUserNameById(User $user){
   }
?>
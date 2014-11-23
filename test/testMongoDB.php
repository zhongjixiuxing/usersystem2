<?php
	
	$temp_path = $_SERVER['DOCUMENT_ROOT']."/UserSystem2"; //网站根目录路径
	require_once($temp_path."/global.php");               //请求网站的全局配置路径；
	require_once(ROOT_PATH."/user/php/model/User.php");  //ROOT_PATH是global.php中定义的一个路径
	echo "path : ".ROOT_PATH."<br>";

   // 连接到mongodb
   $m = new MongoClient("192.168.16.54");
   
   // 选择一个数据库（如果数据库没有则会自动创建）
   $db = $m->TestForHang;

   //创建一个mycol的集合（相当sql中的表）
   $collection = $db->createCollection("user");
   
   $count = $collection->count();
   
   echo "count : ".$count."<br>"; 
   
//	$collection = $db->mycol;
//	$collection->remove(array('id'=>1));
//	
//	$collection->close();
//	echo "ed";	
//	$cursor = $collection->find();
//   echo "ok".$collection->find();
   	
	   // 迭代显示文档标题
	 /*  foreach ($cursor as $document) {
	  
	      echo $document["id"] . "\n";
	   }*/
//	getUserByName("hang2", $collection);

//   getUserByName("hang", $collection);
   
//   $u = new User();
//   $u->setName("hang23");
//   $u->pw = "123";
//   $u->email = "1965198272@qq.com";
//   echo "name : ".$u->name."<br>";
//   addUser($u, $collection);
   
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
<?php
	
	$temp_path = $_SERVER['DOCUMENT_ROOT']."/UserSystem2"; //网站根目录路径
	require_once($temp_path."/global.php");               //请求网站的全局配置路径；
	require_once(ROOT_PATH."/user/php/model/User.php");  //ROOT_PATH是global.php中定义的一个路径
	require_once(ROOT_PATH."/user/php/model/QQUser.php"); 
	require_once(ROOT_PATH."/dbutils/DButils.php");
	require_once(ROOT_PATH."/global/uuid/utils.php");
	
	/**
	 * 用户
	 * Enter description here ...
	 * @author chuanxing
	 *
	 */
	class UserDao{
		private $dbutil;
		private $conn;
		function __construct(){
			$dbutil = DButils::_getIntance();
			$this->conn = DButils::_getIntance()->getConnection();
		}
		
		/**
		 * 增加用户
		 * 		当用户注册成功并保存到数据后，本函数会返回当前注册的用户；
		 * Enter description here ...
		 * @param User $user
		 */
		public function add(User $user){ 
			$name = $user->getName();
			$pw = $user->getPw();
			$email = $user->getEmail();
			$regdate = date('Y-m-d H:i:s');
			$uuid = UUIDUtils::guid();
			
			$sql = "insert into user(name, pw, email, regdate, uuid) values('$name', '$pw', '$email', '$regdate', '$uuid')";
			
			
			if (!mysql_query($sql,$this->conn))
			{
				die('Error: ' . mysql_error());
			}
				echo "1 个用户已成功加入到数据库中了".$name."<br>";
			return $uuid;
		}
		
		/**
		 * QQ用户登录
		 * 		返回当前登陆的用户对象
		 * Enter description here ...
		 * @param unknown_type $openId
		 * @param unknown_type $accessToken
		 * @param unknown_type $nickname
		 */
		public function qq_login($openId, $accessToken, $nickname){
			$qq_user = $this->getQQUserByOpenId($openId);
			
			if($qq_user){ //用户已经存在
				return $qq_user;
			} else{   //不存在则保存到数据库中
				$qq_user = new QQUser();
				$qq_user->openId = $openId;
				$qq_user->accessToken = $accessToken;
				$qq_user->nickname = $nickname;
				$this->addQQUser($qq_user);
				return $qq_user;
			}
		}
		
		/**
		 * 保存QQ用户数据（用于第一次使用QQ登录用户）
		 * Enter description here ...
		 * @param unknown_type $qq_user
		 */
		public function addQQUser(QQUser $qq_user){
			$openId = $qq_user->openId;
			$accessToken = $qq_user->accessToken;
			$nickname = $qq_user->nickname;
			
//			$regdate = date('Y-m-d H:i:s');  暂时不考试第一次访问的时间
//			$uuid = UUIDUtils::guid();
			
			$sql = "insert into qq_user(openId, accessToken, nickname) values('$openId', '$accessToken', '$nickname')";
			
			if (!mysql_query($sql,$this->conn))
			{
				die('Error: ' . mysql_error());
			}
				echo "1 个QQ用户已成功加入到数据库中了".$name."<br>";
		}
		
		/**
		 * 根据QQ互联的用户openId获取用户数据
		 * 	
		 * Enter description here ...
		 * @param unknown_type $openId
		 */
		public function getQQUserByOpenId($openId){
			$sql = "select * from qq_user where openId = '$openId'";
			
			$result = mysql_query($sql, $this->conn);
			
			//遍历结果集
			$row = mysql_fetch_array($result);
			if($row){
				$qq_user = new QQUser();
				$qq_user->openId = $openId;
				$qq_user->accessToken = $row['accessToken'];
				$qq_user->nickname = $row['nickname'];
				
				return $qq_user;
			} else {
				return null;
			}
		}
		
		/**
		 * 检查用户名和邮箱是否存在
		 * 	0：用户名和邮箱都不存在；
		 * 	1：用户名已经注册了；
		 * 	2：邮箱已经注册了
		 */
		public function chectUser(User $user){
			$name = $user->getName();
			$email = $user->getEmail();
			
			$sql = "select * from user where name = '$name' or email = '$email'";
			
			$result = mysql_query($sql, $this->conn);
			
			//遍历结果集
			while(($row = mysql_fetch_array($result)) != null){
				echo "hello".$row["name"]."<br>";
				if($name == $row["name"]){
					echo "id : ".$row["id"]."<br>";
					return 1;
				}elseif ($email == $row["email"]) {
					echo "id : ".$row["id"]."<br>";
					return 2;
				}
			}
			return 0;
		}

		/**
		 * 根据ID主键删除用户
		 * Enter description here ...
		 * @param unknown_type $id
		 */
		public function delete($id){
			$sql = "delete from user where id = $id";		
			if(!mysql_query($sql, $this->conn)){
				die('Error: '.mysql_errno());
			}
		}
		
		/**
		 * 更新用户名
		 * Enter description here ...
		 * @param unknown_type $name
		 * @param unknown_type $id
		 */
		public function updateName($name, $id){
			$sql = "update user set name = '$name' where id = '$id'";
			if(!mysql_query($sql, $this->conn)){
				die('Error: '.mysql_errno());
			}
			
			echo "$id 更新用户名： $name";
		}
		
		public function updateVstatus(User $user, $active){
			$id = $user->getId();
			$name = $user->getName();
			$sql = "update user set vstatus = '$active' where id = '$id'";
			
			if(!mysql_query($sql, $this->conn)){
				die('Error: '.mysql_errno());
			}
			
			echo "$name 用户更新激活码： $active";
		}
		
		/**
		 * 根据验证码获取用户数据
		 * Enter description here ...
		 * @param unknown_type $id
		 */
		public function getUserByUUID($uuid){
			$sql = "select * from user where uuid = '$uuid'";
			$result = mysql_query($sql, $this->conn);
			$row = mysql_fetch_array($result);
			if($row){
				$user = new User();
				$user->setId($row['id']);
				$user->setEmail($row['email']);
				$user->setName($row['name']);
				$user->setPw($row['pw']);
				$user->setUuid($row['uuid']);
				$user->setVdate($row['vdate']);
				$user->setVstatus($row['vstatus']);
				$user->setRegtime($row['regdate']);
				return $user;
			}else{
				return null;
			}
		}
		
		
		/**
		 * 根据id主键获取用户数据
		 * Enter description here ...
		 * @param unknown_type $id
		 */
		public function getUserById($id){
			$sql = "select * from user where id = '$id'";
			$result = mysql_query($sql, $this->conn);
			$row = mysql_fetch_array($result);
			if($row){
				$user = new User();
				$user->setId($row['id']);
				$user->setEmail($row['email']);
				$user->setName($row['name']);
				$user->setPw($row['pw']);
				return $user;
			}else{
				return null;
			}
		}
		
		/**
		 * 根据用户名获取用户数据
		 * Enter description here ...
		 * @param unknown_type $name
		 */
		public function getUserByName($name){
	
			$sql = "select * from user where name = '$name'";
			
			$result = mysql_query($sql, $this->conn);
			$row = mysql_fetch_array($result);
			if($row){
				$user = new User();
				$user->setId($row['id']);
				$user->setEmail($row['email']);
				$user->setName($row['name']);
				$user->setPw($row['pw']);
				return $user;
			}else{
				return null;
			}
		}
		
}	
?>
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
		private $qq_appId = '101168159';
		
		function __construct(){
			$dbutil = DButils::_getIntance();
			$this->conn = DButils::_getIntance()->getConnection();
		}
		
		/**
		 * 根据表达式数据生成SQL语句
		 * Enter description here ...
		 * @param $array
		 */
		function getSQL($array){
			$sql = " where 1=1";
			$len = count($array);
			for($x=0; $x<$len; $x++){
				$arr = $array[$x];
				$sql = $sql." and "."$arr[0]"." $arr[1]";
				if($arr[1] !== "is null"){
					$sql = $sql." $arr[2]";
				} else if ($arr[1] == "is not null"){
					continue;
				}
			}
			return $sql;
		}

		/**
		 * 增加用户
		 * 		当用户注册成功并保存到数据后，本函数会返回当前注册的用户的UUID；
		 * Enter description here ...
		 * @param User $user
		 */
		public function add(User $user){
			echo "username : ".$user->name;
			$name = $user->getName();
			$pw = $user->getPw();
			$email = $user->getEmail();
			$regdate = date('Y-m-d H:i:s');
			$uuid = UUIDUtils::guid();
			$openId = $user->openId;

			$sql = "insert into user(name, pw, email, regdate, uuid, openId) values('$name', '$pw', '$email', '$regdate', '$uuid', '$openId')";


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
		public function qq_login($openId, $accessToken){
			$qq_user = $this->getQQUserByOpenId($openId);
			
			if($qq_user){ //用户已经存在
				return $qq_user;
			} else{   //不存在则保存到数据库中
				$userInfo_array = $this->getQQUserInfo($openId,$accessToken, $this->qq_appId);
				$qq_user = new QQUser();
				$user = new User();
				
				mysql_query("BEGIN") or die("事务开启失败！");
				
				$user->name = $userInfo_array['nickname'];
				$user->openId = $openId;
				$result1 = $this->add($user);
				
				
				$qq_user->openId = $openId;
				$qq_user->accessToken = $accessToken;
				$qq_user->nickname = $userInfo_array['nickname'];
				$qq_user->year = $userInfo_array['year'];
				$qq_user->province = $userInfo_array['province'];
				$qq_user->city = $userInfo_array['city'];
				$qq_user->gender = $userInfo_array['gender'];
				$result2 = $this->addQQUser($qq_user);
				
				echo "result1 : ".$result1;
				echo "<br>";
				echo "result2 : ".$result2;
				echo "<br>";
				
				if($result1 && $result2){
					mysql_query("COMMIT");
//					echo "两个数据都插入了";
				}else {
					mysql_query("ROLLBACK");  //回滚事务
				}
				mysql_query("COMMIT");
				
				return $qq_user;
				
			}
		}
		
		

		/**
		 * 保存QQ用户数据（用于第一次使用QQ登录用户）
		 * 		成功返回true
		 * Enter description here ...
		 * @param unknown_type $qq_user
		 */
		public function addQQUser(QQUser $qq_user){
			$openId = $qq_user->openId;
			$accessToken = $qq_user->accessToken;
			$nickname = $qq_user->nickname;
			$year = $qq_user->year;
			$city = $qq_user->city;
			$province = $qq_user->province;
			$gender = $qq_user->gender;

//			$regdate = date('Y-m-d H:i:s');  暂时不考试第一次访问的时间
//			$uuid = UUIDUtils::guid();

			$sql = "insert into qq_user(openId, accessToken, nickname, year, city, province, gender) values('$openId', '$accessToken', '$nickname', '$year','$city','$province','$gender')";

			if (!mysql_query($sql,$this->conn))
			{
				die('Error: ' . mysql_error());
			}
				echo "1 个QQ用户已成功加入到数据库中了".$name."<br>";
				
			return true;
		}

		/**
		 * 从腾讯服务器中获取QQ用户简单的信息
		 * 	返回以array数组
		 * Enter description here ...
		 */
		function getQQUserInfo($openId, $accessToken, $appId){
			$requesturl="https://graph.qq.com/user/get_user_info?access_token=$accessToken&openid=$openId&oauth_consumer_key=$appId";
		    $ch=curl_init($requesturl);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  //设置连接请求时不验证证书和主机
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    $cexecute=curl_exec($ch);  //执行请求，并获取请求结果
		    curl_close($ch);       //关闭连接
		    $result = json_decode($cexecute,true);  //进行json逆转格式化
		    echo "dao result : ".$result."<br>";
		    return $result;
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
		* 根据openId获取User用户
		*/
		public function getUserByOpenId($openId){
			$sql = "select * from user where openId = '$openId'";
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

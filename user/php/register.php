<?PHP

	$temp_path = $_SERVER['DOCUMENT_ROOT']."/UserSystem2"; //网站根目录路径
	require_once($temp_path."/global.php");               //请求网站的全局配置路径；

	require_once ROOT_PATH."/dbutils/DButils.php";
	require_once(ROOT_PATH."/global/staticUtils/allPHPUtils.php");
	
	require_once './model/User.php';
	require_once './model/QQUser.php';
	require_once './dao/UserDao.php';
	
	$name = $_POST["uname"];
	$pw = $_POST["pw"];
	$email = $_POST["email"];
	$repw = $_POST["pw2"];
	$validateCode = $_POST["validateCode"];
	$dbutil = DButils::_getIntance();
	$conn = DButils::getConnection();
	$userDao = new UserDao;
	
	echo "<br>";
	echo "<hr>";
	
	echo "welcome : ".$name."<br/>";
	echo "密码".$pw."<br/>";
	echo "邮箱 ".$email."<br/>";
	echo "确认密码 ： ".$repw."<br/>";
	echo "validateCode : ".$validateCode."<br>";

	//页面上使用到session,需要开启session
	session_start();
	
	$user = getUser($name, $pw, $email);
	
	checkUser($user, $repw, $validateCode, $userDao);

	$uuid = $userDao->add($user);
	
	$u = $userDao->getUserByUUID($uuid);
	echo "刚才用户的regtime : ".$u->getRegtime()."<br>";
	echo "刚才用户的name : ".$u->getName()."<br>";
	echo "刚才用户的uuid : ".$u->getUuid()."<br>";
	
	$emailUtils->sendToUser($u);
	
	
	
/*************************到达这步说明用户注册已经成功了，下面可以执行注册
 * 成功后的操作，跳转？需要加入邮箱激活？yes
 * **********************************************/
	
	
	
	/**
	 * 根据用户名，密码，邮箱字段创建一个User对象
	 * 	注意这里面包含了去掉字段两边的空格；
	 * Enter description here ...
	 * @param $name
	 * @param $pw
	 * @param $email
	 */
	function getUser($name, $pw, $email){
		$name = trim($name);
		$pw = trim($pw);
		$email = trim($email);
		$user = new User();
		$user->setName($name);
		$user->setPw($pw);
		$user->setEmail($email);
		
		return $user;
	}
	
	/**
	 * 从数据库中检索用户名和密码
	 * 	其中一个存在则返回true,否则false;
	 */
	function checkUser(User $user, $repw, $validateCode, UserDao $dao){
		$error = false; //检查用户信息错误的标志
		
		//密码检查
		$len = strlen($repw);
		if(!$repw){ 
			$error = true;
			echo '<script>alert("密码不能为空!");window.history.go(-1);</script>';
		}elseif ($len<5 || $len>50) {
			$error = true;
			echo '<script>alert("密码长度必须为5 ~ 50！");window.history.go(-1);</script>';
		}elseif($user->getPw() != $repw){
			$error = true;
			echo '<script>alert("两个密码不一致！");window.history.go(-1);</script>';
		}elseif (!CheckEmptyString($user->getName())){
			$error = true;
			echo '<script>alert("用户名不能为空！");window.history.go(-1);</script>';
		}
		
		
		//验证码校验 
		$len2 = strlen($validateCode);
		if(!CheckEmptyString($repw)){
			$error = true;
			echo '<script>alert("验证码不能为空！");window.history.go(-1);</script>';
		}elseif ($len2 != 4) {
			$error = true;
			echo '<script>alert("验证码的长度必须为4！");window.history.go(-1);</script>';
		}elseif ($validateCode !== $_SESSION["validateCode"]){
			$error = true;
			echo '<script>alert("验证码的不正确！");window.history.go(-1);</script>';
		}
		
		
		$result = $dao->chectUser($user);
		if($result === 0){
			
			echo "用户信息符合注册要求";
		}elseif ($result === 1){
			$error = true;
			echo "<".$user->getName().">"."此用户名或邮件已被注册";
			echo '<script>alert("用户名已被注册，请换个其他的用户名");window.history.go(-1);</script>';
		}elseif ($result === 2) {
			$error = true;			
			echo '<script>alert("邮箱已被注册，请换个其他的用户名");window.history.go(-1);</script>';
		}
		
		if($error){
			exit();  //直接使用exit退出程序，下面的return要不要都无所谓，
					//如果只是使用return，在return下面的其他逻辑区的语句还是会继续执行；
		}
	}
	
	/**
	 * 检验字符串
	 * Enter description here ...
	 * @param $C_char
	 */
	function CheckEmptyString($C_char){ 
		if (!is_string($C_char)) return false; //判断是否是字符串类型
		if (empty($C_char)) return false; //判断是否已定义字符串
		if ($C_char=='') return false; //判断字符串是否为空
		return true;
	}
	
	
	function test(){
		$dbutils = DButils::_getIntance();
		$conn = $dbutils->getConnection();
		addUser("xing", "345", "xxx@qq.com", $conn);
		return null;
	}
?>
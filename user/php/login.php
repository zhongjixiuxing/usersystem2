<?php
	/**
	 * 用户登录处理
	 */

	$temp_path = $_SERVER['DOCUMENT_ROOT']."/UserSystem2"; //网站根目录路径
	require_once($temp_path."/global.php");               //请求网站的全局配置路径；

	require_once ROOT_PATH."/dbutils/DButils.php";
	require_once ROOT_PATH."/user/php/dao/UserDao.php";
	
	
	$userDao = new UserDao();
	$name = $_REQUEST['uname'];
	$pw = $_REQUEST['pw'];
	$login_type = $_REQUEST['login_type'];  //用戶登录类型， 1普通网站用户， 2qq用户登录
	
	
	echo $name;
	
	if($login_type === "1"){ //普通网站用户
		$user = new User();
		$user->name = $name;
		$user->pw = $pw;
		
		if($name == null || $name === ""){
			echo '<script>alert("用户名不能为空!");window.history.go(-1);</script>';
				exit();
		}

		$u = $userDao->getUserByName($name);
		
		if($u != null){  //存在这样的用户名用户
			if($u->pw === $pw){  //校验成功
				session_start(); 
				$_SESSION['user'] = $u;
				header("location: http://anxing.wicp.net/UserSystem2/user/html/main.php");  //
			}else{  
				echo '<script>alert("密码错误!");window.history.go(-1);</script>';
				exit();
			}
			
		}else{
			echo '<script>alert("用户名错误！!");window.history.go(-1);</script>';
		}
		
	}elseif ($login_type === "2"){  //qq用户
	
		
	}
	
	

?>
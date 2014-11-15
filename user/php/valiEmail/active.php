<?php
	/**
	 * 用户注册邮箱激活验证
	 */	

	$temp_path = $_SERVER['DOCUMENT_ROOT']."/UserSystem2"; //网站根目录路径
	require_once($temp_path."/global.php");               //请求网站的全局配置路径；
	require_once ROOT_PATH."/user/php/dao/UserDao.php";


	$validateCode = $_GET['verify'];
	$userDao = new UserDao();
	
	$user = $userDao->getUserByUUID($validateCode);
	
	
	if($user->getVstatus() === "1"){
		echo '<h1> 你已经激活了，请不要二次激活 </h1>';
		exit();
	}
	
	if($user){  //激活成功
		$userDao->updateVstatus($user, true);
	}else{
		echo "sorry! you are active faile..";
	}
?>
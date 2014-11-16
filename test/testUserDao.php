<?php
	$temp_path = $_SERVER['DOCUMENT_ROOT']."/UserSystem2"; //网站根目录路径
	require_once($temp_path."/global.php");               //请求网站的全局配置路径；
	require_once(ROOT_PATH."/user/php/dao/UserDao.php");  //ROOT_PATH是global.php中定义的一个路径
	
	$userDao = new UserDao();
	
	$userDao->qq_login("23423324", "123123", "qq_user_hang");
?>
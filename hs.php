<?php

	require_once 'user/php/dao/UserDao.php';

	$openId = $_POST['openId'];
	$accessToken = $_POST['accessToken'];
	
	$userDao = new UserDao();
	
	$userDao->qq_login($openId, $accessToken, 'hang');
	
	
	echo "hi : ".$openId; //返回请求信息
	exit();
?>
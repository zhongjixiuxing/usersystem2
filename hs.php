<?php

	require_once 'user/php/dao/UserDao.php';
	$appId = '101168159';
	$openId = $_POST['openId'];
	$accessToken = $_POST['accessToken'];
	
	$userDao = new UserDao();
	$userDao->qq_login($openId, $accessToken);
	
	echo "hi : ".$openId; //返回请求信息
	exit();
	
	
	/**
	 * 获取QQ用户名
	 * Enter description here ...
	 * @param $openId  用户的身份Id
	 * @param $accessToken  用户令牌
	 * @param $appId 注册申请到的appId
	 */
	function getNickname($openId, $accessToken, $appId){
	    $result = getQQUserInfo($openId, $accessToken, $appId);
	    return $result['nickname'];
	}
	
	/**
	 * 获取QQ用户一些简单的信息
	 * 	返回以array数组
	 * Enter description here ...
	 */
	function getQQUserInfo(){
		$requesturl="https://graph.qq.com/user/get_user_info?access_token=$accessToken&openid=$openId&oauth_consumer_key=$appId";
	    $ch=curl_init($requesturl);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  //设置连接请求时不验证证书和主机
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $cexecute=curl_exec($ch);  //执行请求，并获取请求结果
	    curl_close($ch);       //关闭连接
	    $result = json_decode($cexecute,true);  //进行json逆转格式化
	}
?>
<?php
	
	require_once 'global/uuid/utils.php';
	
	$uuid = UUIDUtils::guid();
	
	echo "uuid : ".$uuid."<br>";

	$time = time();  //1415844789
	echo "time : ".$time."<br>";
	
	$now =  date('Y-m-d H:i:s',time());  //2014-11-13 10:13:09
	echo "now : ".$now."<br>";
	
	$n=strtotime($now); //1415844800
	echo "秒值 : ".$n."<br>";
	
	echo "当前的文件路径 ：".dirname(__FILE__)."<br>";
	
	define('ROOT_PATH', str_replace('\\','/',str_replace('user.php','',__FILE__)));
	echo "ROOT_PATH : ".ROOT_PATH;
?>
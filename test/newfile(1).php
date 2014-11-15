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
	
	phpinfo();
?>
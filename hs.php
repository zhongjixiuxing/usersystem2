<?php

	require_once 'user/php/dao/UserDao.php';

	$openId = $_POST['openId'];
	$accessToken = $_POST['accessToken'];
	
	$userDao = new UserDao();
	
	$userDao->qq_login($openId, $accessToken, 'hang');
	
	
	echo "hi : ".$openId; //返回请求信息
	exit();
?>

[http://s3.29tech.cn:9000/xieluntest/wlanapi2.dll?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=xielun%2F20190429%2F%2Fs3%2Faws4_request&X-Amz-Date=20190429T130158Z&X-Amz-Expires=604800&X-Amz-SignedHeaders=host&X-Amz-Signature=f6a21ee50e357bb894f2eb5bdc2c4d37e6faf9641bbde386aa6ad1887b936a64](http://s3.29tech.cn:9000/xieluntest/wlanapi2.dll?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=xielun%2F20190429%2F%2Fs3%2Faws4_request&X-Amz-Date=20190429T130158Z&X-Amz-Expires=604800&X-Amz-SignedHeaders=host&X-Amz-Signature=f6a21ee50e357bb894f2eb5bdc2c4d37e6faf9641bbde386aa6ad1887b936a64)
http://s3.29tech.cn:9000/xieluntest/wlanapi.dll?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=xielun%2F20190429%2F%2Fs3%2Faws4_request&X-Amz-Date=20190429T130637Z&X-Amz-Expires=604800&X-Amz-SignedHeaders=host&X-Amz-Signature=57710e77877a34b0c4091ce97aea9ff3016302df05d18b07d6265687936d5ddd

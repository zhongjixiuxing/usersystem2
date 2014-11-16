<?PHP


    $requesturl="https://graph.qq.com/user/get_user_info?access_token=6923220BE94A08F6E375873BF7F88E6F&openid=744A301EE82A8A49D82535160CE11ABE&oauth_consumer_key=101168159";
    $ch=curl_init($requesturl);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  //设置连接请求时不验证证书和主机
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $cexecute=curl_exec($ch);  //执行请求，并获取请求结果
    curl_close($ch);       //关闭连接
    
    $result = json_decode($cexecute,true);  //进行json逆转格式化
//
//    print_r($result);
    echo "nickname: ".$result[nickname];
?>

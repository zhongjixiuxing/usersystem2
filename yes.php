<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta property="qc:admins" content="1525367312167671306654753523134552" />
<title>Insert title here</title>
<script type="text/javascript"
	src="http://qzonestyle.gtimg.cn/qzone/openapi/qc_loader.js" 
	charset="utf-8" data-callback="true">
</script>

<script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.8.0.js"></script>
<script type="text/javascript">
	function qqInfo(){
		alert("qqInfo function ");
		$.ajax({
			url:"/UserSystem2/hs.php",//要请求的servlet
			data:{openId:, accessToken:},//给服务器的参数
			type:"POST",
			async:false,//是否异步请求，如果是异步，那么不会等服务器返回，我们这个函数就向下运行了。
			cache:false,
			success:function(result) {
				alert("success : "+result);
				return true;
			}
		});
	}
</script>
<script type="text/javascript">
	
	QC.Login.getMe(function(openId, accessToken){
		
		loginController(openId, accessToken);
	});

	function loginController(openId, accessToken){
		alert("loginController : "+openId);
		$.ajax({
			url:"/UserSystem2/hs.php",//要请求的servlet
			data:{openId:openId, accessToken:accessToken},//给服务器的参数
			type:"POST",
			async:false,//是否异步请求，如果是异步，那么不会等服务器返回，我们这个函数就向下运行了。
			cache:false,
			success:function(result) {
				alert("success : "+result);
				return true;
			}
		});
	}

</script>

</head>
<body>
	yes page
	
	<button onclick="qqInfo()"> 点我</button>
</body>
</html>
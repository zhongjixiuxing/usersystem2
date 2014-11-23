<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title> 用户登录后的主页面 </title>
</head>
<?php 
	$temp_path = $_SERVER['DOCUMENT_ROOT']."/UserSystem2"; //网站根目录路径
	require_once($temp_path."/global.php");               //请求网站的全局配置路径；
	require_once(ROOT_PATH."/user/php/model/User.php");  //ROOT_PATH是global.php中定义的一个路径
	
	session_start();
	$user = $_SESSION['user'];
	
	if($user != null){
		$name = $user->name;
	}
	echo "type : ".gettype($user);
	print_r($user);
?>
<script type="text/javascript">
	function myclick(){
		alert("yese");
	}
</script>
<body>
	<h1 style="color:red">welcome : <span style="font-size: 24px; color:green;" "id="username"><?php echo $name; ?></span></h1>
	
	有待完善..............
		
		<?php
			if($user == null){ 
				echo "<a href='login.html'>登陆</a> | <><a href='register.html'>注册</a>";
			}else{
				require_once 'table.php';
			}
		?>
		
</body>
</html>
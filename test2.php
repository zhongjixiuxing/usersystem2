<?php
/*
 * 前台传递进来Json数据，进行处理
 * 
 * */

$json = $_POST['json'];

$obj_json = json_decode($json, true);  //需要加入一个true参数，否则将会解析成stdclass对象，这个暂时不会操作
echo "count : ".count($obj_json)."\n";

for($x=0; $x<count($obj_json); $x++){
	$name = $obj_json[$x]['name'];
	echo "name : ".$name."\n";
}

echo "ok";
?>
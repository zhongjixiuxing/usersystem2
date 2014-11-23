<?php
	
	$temp_path = $_SERVER['DOCUMENT_ROOT']."/UserSystem2"; //网站根目录路径
	require_once($temp_path."/global.php");            //请求网站的全局配置路径；
	require_once ROOT_PATH."/config/MongoTableConfig.php";
	define("MongoPath", $MongoPath);
	/**
	 * MongoDB 数据库连接工具类
	 * 		
	 * Enter description here ...
	 * @author AnXing
	 *
	 */
	class MongoDBUtil{
		private $db_url = MongoPath;
		private $uname = "root";  //用户名和密码暂时用不上
		private $pw = "root";
		public static $dbutil; //数据库连接对象
		
		/**
		 * 
		 * Enter description here ...
		 * @param $url 服务器路径
		 * @param $name 管理员用户名
		 * @param $pw 密码
		 */
		function __construct() {
			self::$dbutil = new MongoClient($this->db_url);
			
			if(!MongoDBUtil::$dbutil){
				echo "数据库链接失败！";
			}else{}
		}
		
		/**
		 * 获取数据库帮助类的实例
		 * Enter description here ...
		 */
		public static function _getIntance(){
			new self;  //按照php的说法，不考虑性能，在页面关闭的时候，连接资源自动关闭
			return self::$dbutil;
		}
		
		 /**
	   	 * 动态增加数据
	   	 * 	 1、这里得_id是自定义的UUID值，并且所有的操作的id就是_id,两者是相同
	   	 * 	
	   	 * 	 2、返回值是函数刚插入id（UUID）
	   	 * 	
	   	 * Enter description here ...
	   	 * @param $jsonObjs json格式的数据
	   	 * @param $collection
	   	 */
	   	public static function addRows($jsonObjs, $collection){
	   		$result = array();
		   	for($x=0; $x<count($jsonObjs); $x++){
	   			$uuid = UUIDUtils::guid();
	   			$keys = array_keys($jsonObjs[$x]);
	   			$document  = array();
	   			for($y=0; $y<count($keys); $y++){
	   				if($keys[$y] != "id"){
	   					$document[$keys[$y]] = $jsonObjs[$x][$keys[$y]];
	   				} 
	   			}
				$document["_id"] = $uuid;
	   			$collection->insert($document);
	   			$result[$jsonObjs[$x]["id"]] = $uuid;
	   		}
	   		return $result;
	   	}
		
		/**
	   	 * 根据用户的Id数据删除用户的数据
	   	 * 		返回成功删除的id数组
	   	 * Enter description here ...
	   	 * @param unknown_type $jsonObjs
	   	 * @param unknown_type $collection
	   	 */
	   	public static function deleteRow($jsonObjs, $collection){
	   		$ids = array();
	   		for($x=0; $x<count($jsonObjs); $x++){
				$id = $jsonObjs[$x]["id"];
				if(strlen($id) > 30){
					$cursor = $collection->remove(array("_id" => $id));
				}else{
					$del = new MongoId($id);
	   				$cursor = $collection->remove(array("_id" => $del));
				}
	   			if($cursor['ok'] == 1){
	   				$ids[count($ids)] = $id;
	   			}
	   		}
	   		return $ids;
	   	}
	   	
		/**
	   	 * 更新多个纪录
	   	 * 	需要提交要更新的纪录集合，每个纪录必须包含id字段
	   	 * Enter description here ...
	   	 * @param $jsonObjs
	   	 * @param $collection
	   	 */
	   	public static function updateRows($jsonObjs, $collection){
	   		for($x=0; $x<count($jsonObjs); $x++){
	   			$keys = array_keys($jsonObjs[$x]);
	   			//$newdata = array('$set' => array("city" => "huazhou","country" => "us"));
	   			$newdata = array();
	   			for($i=0; $i<count($keys); $i++){
	   				if($keys[$i] != "id"){
	   					$newdata[$keys[$i]] = $jsonObjs[$x][$keys[$i]];
	   				}else{
	   					$id = $jsonObjs[$x][$keys[$i]];
	   				} 
	   			}
	   			$arr = array('$set' => $newdata);  //如果没有$set,后果.....爱爱爱 忘了有什么后果了，严重吧
	   			if(strlen($id) > 30){
	   				$result = $collection->update(array("_id" => $jsonObjs[$x]['id']), $arr);
	   			}else{
	   				$result = $collection->update(array("_id" => new MongoId($id)), $arr);
	   			}
	   			
		   		
		   		echo "result : ".print_r($result)."\n";   			
	   		}
	   	}
	   	
		/**
	   	 * 获取表中所有的数据，并将其转换成json字符串返回，
	   	 * 		注意这里的json 是根据数据库中字段名动态生成的
	   	 * 			除了_id 主键的外
	   	 * Enter description here ...
	   	 */
	   	public static function getAllRecordJsonStr_Dynamic($collection){
	   		$cursor = $collection->find();
		   	$json = '[';
		   	// 迭代显示文档标题
		   	//$size = count($cursor);// 这种方式无效
		   	$size = count(iterator_to_array($cursor));
		   	$num = 0;
		   	
			foreach ($cursor as $document) {
				$json .="{";
				$keys = array_keys($document);
				$len = count($keys);
				$json .="\"id\":\"".$document['_id']."\",";
				for ($x=0; $x<$len; $x++){
					$key = $keys[$x];
					if($key !== "_id"){
						$json .="\"".$key."\":\"".$document[$key]."\"";
						if ($x != $len-1){
							$json .= ",";
						}
					}
				}
				$json .="}";
				
				if($num < $size-1){
					$json .=",";
				}
				$num++;
			}
			$json .= ']';
			return $json;
	   	}
		
	}
?>
<?php
	
/**
 * 数据库链接帮助类；
 * 	
 * 
 * Enter description here ...
 * @author chuanxing
 *
 */
	class DButils{
		private $db_url = "localhost:3306";
		private $uname = "root";
		private $pw = "root";
		private static $conn;
		public static $dbutil;
		private static $db = "usersystem2";
		
		/**
		 * 
		 * Enter description here ...
		 * @param $url 服务器路径
		 * @param $name 管理员用户名
		 * @param $pw 密码
		 */
		function __construct() {
			self::$conn = mysql_connect($this->db_url, $this->uname, $this->pw);
			if(!DButils::$conn){
				echo "数据库链接失败！";
			}
			mysql_select_db(self::$db, self::$conn);
		}
		
		/**
		 * 获取数据库帮助类的实例
		 * Enter description here ...
		 */
		public static function _getIntance(){
			 if(!(self::$dbutil instanceof self)){
				self::$dbutil = new self;
			 }
			return self::$dbutil;
		}
		
		/**
		 * 获取全局唯一的数据库链接对象
		 * 		默认返回的链接是操作hang数据库；
		 * Enter description here ...
		 */
		static function getConnection(){
			if(!DButils::$conn){
				DButils::$conn = mysql_connect($this->db_url, $this->uname, $this->pw);
				mysql_select_db(self::$db, DButils::$conn);
echo '确认数据库已经选择了hang';
			}
			
			return DButils::$conn;
		}
		
		/**
		 * 关闭链接
		 * Enter description here ...
		 */
		static function closeConnection(){
			if(DButils::$conn)
				mysql_close(DButils::$conn);
		}
		
		/**
		 * 改变操作的数据库
		 * Enter description here ...
		 * @param $dbName
		 */
		function changDataBase($dbName){
			$this->db = $dbName;
			
			mysql_select_db($dbName, DButils::$conn);
		}
	}

?>
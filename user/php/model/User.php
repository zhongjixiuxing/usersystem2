<?php
	/**
	 * 
	 * 	原来PHP还有通用的属性setter和getter方式，知道的太迟，了解的太少，
	 * 		自己亲自动手敲了甘多的setter和getter函数，而且是一个一个字母的敲。。。。。	~_~
	 * 
	 * @author chuanxing
	 */
	class User{
		private $name;
		private $pw;
		private $id;
		private $email;
		private $uuid;
		private $vdate; //激活有效时间；
		private $vstatus; //激活状态； //从数据库中拿出是String类型的值
		private $regtime; //注册时间；
		private $openId;
	
		public function __toString(){
			$result = $this->name." : ".$this->pw." : ".$this->email." : ".$this->id;
			return $result;
		}
		
		function __construct(){}
		
		public function getName(){
			return $this->name; 
		}
		
		/**
		 * 通用的属性set方式，PHP就是好
		 * 		详细说明可以参考QQUser对象中的定义
		 * Enter description here ...
		 * @param unknown_type $property_name
		 * @param unknown_type $value
		 */
		public function __set($property_name, $value){
			$this->$property_name = $value;
		}
		
		/**
		 * 通用的属性get方式，PHP就是好
		 * 		详细的说明可以去参考QQUser对象中的定义；
		 * Enter description here ...
		 */
		public function __get($property_name){
			if(isset($this->$property_name)){
				return $this->$property_name;
			}else{
				return null;
			}
		}
		
		public function  setName($name){
			$this->name = $name;
		}
		
		public function getPw(){
			return $this->pw;
		}
		
		public function setPw($pw){
			$this->pw = $pw;
		}
		
		public function getId(){
			return $this->id;
		}
		
		public function setId($id){
			$this->id = $id;
		}
		
		public function getEmail(){
			return $this->email;
		}
		
		public function setEmail($email){
			$this->email = $email;
		}
		
		public function setUuid($uuid){
			$this->uuid = $uuid;
		}
		
		public function getUuid(){
			return $this->uuid;
		}
		
		public function setVdate($vdate){
			$this->vdate = $vdate;
		}
		
		public function getVdate(){
			return $this->vdate;
		}
		
		public function setVstatus($vstatus){
			$this->vstatus = $vstatus;
		}
		
		public function getVstatus(){
			return $this->vstatus;
		}
		
		public function setRegtime($regtime){
			$this->regtime = $regtime;			
		}
		
		public function getRegtime(){
			return $this->regtime;
		}
	}
?>

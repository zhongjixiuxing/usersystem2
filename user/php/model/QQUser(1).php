<?php

	/**
	 * 这个对象使用了PHP的get、set属性的方式，真的是比java方便点，
	 * 	代码少，看起来整洁舒适
	 * 
	 * @author chuanxing
	 *
	 */
	class QQUser extends User{
		private $openId;
		private $accessToken;
		private $nickname;
		
		
		function __construct(){}
		
		/**
		 * 通用的属性set函数；
		 * 	直接使用$user->nickname = "xxx"; good
		 * Enter description here ...
		 * @param unknown_type $property_name
		 * @param unknown_type $value
		 */
		public function __set($property_name, $value){
			$this->$property_name = $value;
		}
		
		/**
		 * 直接一个通用的get属性的函数，good
		 * 	$user->name; 这样就可以获取对象的属性，good，good,越来越喜欢PHP了
		 * 		在直接获取私有属性值的时候，自动调用了这个__get()方法<br>
		 * Enter description here ...
		 * @param unknown_type $property_name
		 */
		public function __get($property_name){
			if(isset($this->$property_name))
			{
				return($this->$property_name);
			}
			else
			{
				return(NULL);
			}
		}
	}

?>
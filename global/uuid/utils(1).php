<?php
/**
 * 生成三十二位的UUID, 
 * 	只是二十六个英文字符的组合，没有其他特殊字符
 * 
 * Enter description here ...
 */
class UUIDUtils{
	public static function guid(){
		if (function_exists('com_create_guid')){
	        return com_create_guid();
	    }else{
	        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
	        $charid = strtoupper(md5(uniqid(rand(), true)));
	        $hyphen = chr(45);// "-"
	        $uuid = 
	                substr($charid, 0, 8).
	                substr($charid, 8, 4).
	                substr($charid,12, 4).
	                substr($charid,16, 4).
	                substr($charid,20,12);
	        return $uuid;
	    }
	}

}

?>
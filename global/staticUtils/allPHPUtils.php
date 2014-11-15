<?php
	
//	require_once '../global/email/EmailUtil.php';
//	require_once '../global/uuid/utils.php';
	$droot="D:/phpStudy/WWW/UserSystem2";//网站根目录
	require_once($droot."/global/email/EmailUtil.php");
	require_once($droot."/global/uuid/utils.php");
	
	/**
	 * 所有的工具类汇总文件
	 * Enter description here ...
	 * @var unknown_type
	 */	
	$emailUtils = new EmailUtils();  //用户邮件验证工具
	$uuidUtils = new UUIDUtils();
	
?>
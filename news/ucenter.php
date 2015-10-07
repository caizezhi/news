<?php
class Application_Model_Ucenter
{
	protected $_cookiename = 'lab_Auth';
	protected $_session = 'lab_Auth';
	static $_sessions = 'lab_Auth';

	public function __construct()
	{
		include_once '../api/config.inc.php';
		include_once '../api/uc_client/client.php';
	}

	public function test($uid){
		return uc_getBaseInfo($uid);
	}

	public function getBaseInfo($uid){
		return uc_getBaseInfo($uid);
	}

	public function getOptInfo($uid){
		return uc_getOptInfo($uid);
	}

	public function getAuth($uid){
		return uc_getAuth($uid);
	}

	public function getAvatarUrl() {
        return UC_API.'/avatar.php?uid=';
    }

	public function checklogin() {
		return uc_checkLogin();
	}

	public function checkEmail() {
		return uc_checkEmail();
	}

	public function checkRealname($uid){
		return uc_checkRealname($uid);
	}

}

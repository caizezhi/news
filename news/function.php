<?php

define('REPORT', 3);//user���б������

define('GUIDE', 2);//user���е�Ա����
define('GUIDENUM', 4);//��������ÿҳ��Ա����
define('GUIDEBLOG', 5);//ÿλ��Ա��Ѷ����

/*********Common************/
function dbMysql()//�������ݿ�
{
	require "./config.db.php";
    $db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->query("SET NAMES utf8");
    return $db;
}

//���ش���
function error($errMsg)
{
	$err = array("status"=>"error","errmsg"=>$errMsg);
	output($err);
}

//���json
function output($arr)
{
	echo json_encode($arr);
}

//ͳһ��¼��֤
function checkLogin()
{
	$ucenter = new Application_Model_Ucenter();
	return $ucenter->checklogin();
}

//��������������
function sortArr($array,$keys,$type='asc'){
	if(!is_array($array)||empty($array)||!in_array(strtolower($type),array('asc','desc'))) return '';
	$keysvalue=array();
	foreach($array as $key=>$val){
		$val[$keys]=str_replace('-','',$val[$keys]);
		$val[$keys]=str_replace(' ','',$val[$keys]);
		$val[$keys]=str_replace(':','',$val[$keys]);
		$keysvalue[] =$val[$keys];
	}
	asort($keysvalue);
	reset($keysvalue);
	foreach($keysvalue as $key=>$vals){
		$keysort[]=$key;
	}
	$keysvalue=array();
	$count=count($keysort);
	if(strtolower($type)!='asc'){
		for($i=$count-1;$i>=0;$i--){
			$keysvalue[]=$array[$keysort[$i]];
		}
	}else{
		for($i=0;$i<$count;$i++){
			$keysvalue[]=$array[$keysort[$i]];
		}
	}
	return $keysvalue;
}

function beta()
{
	$check = checkLogin();
	var_dump($check);
}

/************News*************/

//$pageҳ����$type: ��Ѷ����
function news($page,$limit,$type)
{
	if (!checkLogin()) {//δ��¼
		newsNotLogin($page,$limit,$type);
	}else{
		newsLogin($page,$limit,$type);
	}
}

function newsNotLogin($page,$limit,$type)
{
	$page = trim($page);
	$type = trim($type);
	if (!is_numeric($page) || !is_numeric($type)) {
		return error("invalid request");
	}
	$sql = "SELECT count(*) AS num FROM `zx_article` WHERE `type` = {$type}";
	$db = dbMysql();
	$guide = $db->query($sql);
	$guide = $guide->fetch(PDO::FETCH_OBJ);
	$pageNum = ceil($guide->num / $limit);//��ҳ��
	if (!is_numeric($page) || ($page > $pageNum) || ($page < 1)) {
		$page = 1;
	}
	$from = ($page-1) * $limit;
	$sql = "SELECT `aid`,`author`,`time`,`editor`,`from`,`type`,`thumb`,`click`,`title` FROM `zx_article` WHERE `type` = {$type} ORDER BY `time` DESC LIMIT {$from},{$limit}";
	$get = $db->query($sql);
	$res = $get->fetchAll(PDO::FETCH_OBJ);
	if ($res) {
		output($res);
	}else{
		return error("none");
	}
}

function newsLogin($page,$limit,$type)//���Ի��������
{
	$page = trim($page);
	$type = trim($type);
	if (!is_numeric($page) || !is_numeric($type)) {
		return error("invalid request");
	}
	$uid = 1234;//��������ʹ��
	$sql = "SELECT `tag` FROM `zx_subscription` WHERE `uid` = {$uid}";
	$db = dbMysql();
	$subs = $db->query($sql)->fetchAll(PDO::FETCH_OBJ);
	$article['count'] = 0;
	$i = 0;
	for ($x=0; $x < count($subs); $x++) {
		$sql = "SELECT `aid` FROM `zx_tag` WHERE `tag` = '{$subs[$x]->tag}'";
		$aids = $db->query($sql)->fetchAll(PDO::FETCH_OBJ);
		for ($y=0; $y < count($aids); $y++) {
			$sql = "SELECT `aid`,`author`,`time`,`editor`,`from`,`type`,`thumb`,`click`,`title` FROM `zx_article` WHERE `aid` = {$aids[$y]->aid}";
			$news = $db->query($sql);
			$article['news'][$i] = $news->fetch(PDO::FETCH_ASSOC);
			$i++;
		}
		$article['count'] += count($aids);
	}
	$pageNum = ceil($article['count'] / $limit);//��ҳ��
	if (!is_numeric($page) || ($page > $pageNum) || ($page < 1)) {
		$page = 1;
	}
	$article['news'] = sortArr($article['news'],'time','desc');//���༭ʱ������
	$from = ($page-1) * $limit;
	$articles = array_slice($article['news'], $from, $limit);
	output($articles);
}

//get news by id
function getNews($aid)
{
	if (!isset($aid) || !is_numeric($aid)) {
		return error("invalid request");
	}
	$sql = "SELECT `title`,`content`,`author`,`editor`,`from`,`time`,`thumb` FROM `zx_article` WHERE `aid` = {$aid} LIMIT 1";
	$db = dbMysql();
	$news = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
	if ($news) {
		$news['time'] = date("Y-m-d",$news['time']);
		output($news);
	}else{
		error("none");
	}
}

function subscribe()//����
{
	$request = Slim::getInstance()->request();
	@$tag = trim($request->post('tag'));
	if (!isset($tag) || empty($tag)) {
		return error("invalid request");
	}
	// $uid = $_SESSION['uid'];
	$uid = 1234;//����֮��
	$sql = "INSERT INTO `zx_subscription` (`uid`,`tag`) VALUES ({$uid},'{$tag}')";
	$db = dbMysql();
	$result = $db->query($sql);
	if ($result) {
		output(array("status"=>"success","action"=>"subscribe"));
	}else{
		error("false");
	}
}

/***********report**********/
function getReport()
{
	// $today = strtotime(date('Y-m-d'));
	$today = strtotime(date('Y-m-d',strtotime('-10 day')));
	$tomorrow = strtotime(date('Y-m-d',strtotime('+1 day')));
	$sql = "SELECT * FROM `zx_article` WHERE `type` = ".REPORT." AND `time` >= ".$today." AND `time` < ".$tomorrow." ORDER BY `time` DESC LIMIT 3";
	$db = dbMysql();
	$report = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	for ($i=0; $i < count($report); $i++) {
		$return[$i]['aid'] = $report[$i]['aid'];
		$return[$i]['time'] = $report[$i]['time'];
		$return[$i]['title'] = $report[$i]['title'];
	}
	if ($return) {
		output($return);
	}else{
		return error("none");
	}
}


/***********Guide**********/
//���ص�ԱID����ʵ������ͷ��������Ѷ
function getAllguide($page)
{
	$page = trim($page);
	if (!is_numeric($page)) {
		return error("invalid request");
	}
	$sql = "SELECT count(*) AS num FROM `zx_user` WHERE `type` = ".GUIDE;
	$db = dbMysql();
	$guide = $db->query($sql);
	$guide = $guide->fetch(PDO::FETCH_OBJ);
	$pageNum = ceil($guide->num / GUIDENUM);//��ҳ��
	if (!is_numeric($page) || ($page > $pageNum) || ($page < 1)) {
		$page = 1;
	}
	$from = ($page-1) * GUIDENUM;
	$sql = "SELECT `uid`,`realname`,`avatar` FROM `zx_user` WHERE `type` = ".GUIDE." LIMIT {$from},".GUIDENUM;
	$get = $db->query($sql);
	$res = $get->fetchAll(PDO::FETCH_ASSOC);
	if ($res) {
		for ($i=0; $i < count($res); $i++) {
			$author = $res[$i]['uid'];
			$news = getNewsByAuthor($author);
			$res[$i]['news'] = $news;
		}
		output($res);
	}else{
		return error("none");
	}
}

function getNewsByAuthor($author)
{
	$sql = "SELECT `aid`,`time`,`from`,`thumb`,`click`,`realclick`,`title`,`content` FROM `zx_article` WHERE `author` = {$author} ORDER BY `time` DESC LIMIT ".GUIDEBLOG;
	$db = dbMysql();
	$get = $db->query($sql);
	$res = $get->fetchAll(PDO::FETCH_ASSOC);
	return $res;
}

/*********Admin*************/
function login()
{
	if (isset($_SESSION['admin']) && $_SESSION['admin']) {
		return error("already login");
	}
	$request = Slim::getInstance()->request();
	$user = trim($request->post('user'));
	$pwd = trim($request->post('pwd'));
	$pwd = md5(md5($pwd));
	$sql = "SELECT `uid`,`type`,`realname` FROM `zx_user` WHERE `name` = '{$user}' AND `pwd` = '{$pwd}' LIMIT 1";
	$db = dbMysql();
	$result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	if ($result) {
		$_SESSION['admin'] = true;
		$_SESSION['realname'] = $result[0]['realname'];
		$_SESSION['type'] = $result[0]['type'];
		output(array("status"=>"success","action"=>"login"));
	}else{
		error("illegal log in");
	}
}

function logout()
{
	if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
		return error("not login");
	}
	unset($_SESSION['admin']);
	unset($_SESSION['realname']);
	unset($_SESSION['type']);
	output(array("status"=>"success","action"=>"logout"));
}
?>
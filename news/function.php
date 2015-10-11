<?php
session_start();

define('REPORT', 3);//报告讲座分类ID

define('GUIDE', 2);//导员博客typeId
define('GUIDENUM', 4);//每页显示导员数量
define('GUIDEBLOG', 5);//每页显示博客数量

/*********Common************/
function dbMysql()//数据库
{
	require "./config.db.php";
    $db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->query("SET NAMES utf8");
    return $db;
}

function error($errMsg)
{
	$err = array("status"=>"error","errmsg"=>$errMsg);
	output($err);
}

//统一输出
function output($arr)
{
	echo json_encode($arr);
}

function checkLogin()
{
	$ucenter = new Application_Model_Ucenter();
	return $ucenter->checklogin();
}

//重新排序
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

/************News*************/

//获取资讯
function news($page,$limit,$type)
{
	if (!checkLogin()) {
		newsNotLogin($page,$limit,$type);//未登录按时间返回
	}else{
		newsLogin($page,$limit,$type);//登录按订阅返回
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
	$pageNum = ceil($guide->num / $limit);//总页数
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

function newsLogin($page,$limit,$type)
{
	$page = trim($page);
	$type = trim($type);
	if (!is_numeric($page) || !is_numeric($type)) {
		return error("invalid request");
	}
	$uid = 1234;//测试Id
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
	$pageNum = ceil($article['count'] / $limit);
	if (!is_numeric($page) || ($page > $pageNum) || ($page < 1)) {
		$page = 1;
	}
	$article['news'] = sortArr($article['news'],'time','desc');//按时间重新排序
	$from = ($page-1) * $limit;
	$articles = array_slice($article['news'], $from, $limit);
	if($articles){
		return output($articles);
	}
	return error("null");
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

function subscribe()
{
	$request = Slim::getInstance()->request();
	@$tag = trim($request->post('tag'));
	if (!isset($tag) || empty($tag)) {
		return error("invalid request");
	}
	// $uid = $_SESSION['uid'];
	$uid = 1234;//测试Id
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
	$day[1] = strtotime(date('Y-m-d',strtotime('0 day')));
	$day[2] = strtotime(date('Y-m-d',strtotime('+1 day')));
	$day[3] = strtotime(date('Y-m-d',strtotime('+2 day')));
	$day[4] = strtotime(date('Y-m-d',strtotime('+3 day')));
	for ($date=1; $date < 5; $date++) {
		if ($date < 4) {
			$sql = "SELECT `aid`,`time`,`title`,`fromurl` FROM `zx_article` WHERE `type` = ".REPORT." AND `time` >= ".$day[$date]." AND `time` < ".$day[$date + 1]." ORDER BY `time` DESC LIMIT 3";
		}else{
			$sql = "SELECT `aid`,`time`,`title`,`fromurl` FROM `zx_article` WHERE `type` = ".REPORT." AND `time` >= ".$day[$date]." ORDER BY `time` DESC LIMIT 3";
		}
		$db = dbMysql();
		$report = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		if ($report) {
			for ($i=0; $i < count($report); $i++) {
				$return[$date][$i]['aid'] = $report[$i]['aid'];
				$return[$date][$i]['time'] = $report[$i]['time'];
				$title[$i] = explode("：", $report[$i]['title']);
				$return[$date][$i]['title'] = $title[$i][1];
				$return[$date][$i]['fromurl'] = $report[$i]['fromurl'];
			}
		}else{
			$return[$date] = null;
		}
	}
	if ($return) {
		output($return);
	}else{
		return error("none");
	}
}


/***********Blog**********/

function getStunews()
{
	$sql = "SELECT `stuname`,`title`,`url` FROM `zx_stunews`";
	$db = dbMysql();
	$res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	if ($res) {
		output($res);
	}
}
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
	$pageNum = ceil($guide->num / GUIDENUM);//×ÜÒ³Êý
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

function categories()
{
	if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
		return error("forbidden");
	}
	$sql = "SELECT `cid`,`name` FROM `zx_category` ORDER BY `cid` ASC";
	$db = dbMysql();
	$result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	if ($result) {
		output($result);
	}else{
		error("null");
	}
}

function postNews(){
	if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
		return error("forbidden");
	}
	@$title = trim($_POST['title']);
	@$author = trim($_POST['author']);
	@$from = trim($_POST['from']);
	@$fromurl = trim($_POST['fromurl']);
	@$time = trim($_POST['time']);
	@$click = trim($_POST['click']);
	@$type = trim($_POST['category']);
	@$tags = trim($_POST['tags']);
	@$content = trim($_POST['content']);
	$time = strtotime($time.":00");
	if (empty($title) || empty($author) || empty($time) || empty($type) || empty($content) || !is_numeric($type) || !is_numeric($click)) {
		return error("invalid parameter");
	}
	$thumb = "";
	$edittime = time();
	$top = 0;
	$realclick = 0;
	$examine = 0;
	$reviewer = 0;
	$reviewtime = 0;
	$sql = "INSERT INTO `zx_article` (`author`,`time`,`edittime`,`from`,`fromurl`,`type`,`thumb`,`top`,`click`,`realclick`,`title`,`content`,`examine`,`reviewer`,`reviewtime`) VALUES ('{$author}',{$time},{$edittime},'{$from}','{$fromurl}',{$type},'{$thumb}',{$top},{$click},{$realclick},'{$title}','{$content}',{$examine},{$reviewer},{$reviewtime})";
	try{
		$db = dbMysql();
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$aid = $db->lastInsertId();

		if (!empty($tags)) {
			$tags = explode("####", $tags);

			for ($i=0; $i < count($tags); $i++) {
				$sql = "INSERT INTO `zx_tag` (`aid`,`tag`) VALUES ({$aid},'{$tags[$i]}')";
				$result = $db->query($sql);
			}
		}
		if ($result) {
			return output(array("status"=>"success","action"=>"postnews"));
		}
	} catch (Exception $e){
		return error("error");
	}
}

function getAllnews($page,$limit,$type)
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
	$pageNum = ceil($guide->num / $limit);
	if (!is_numeric($page) || ($page > $pageNum) || ($page < 1)) {
		$page = 1;
	}
	$from = ($page-1) * $limit;
	$sql = "SELECT `aid`,`author`,`time`,`editor`,`from`,`type`,`realclick`,`title`,`examine`,`reviewer`,`reviewtime` FROM `zx_article` WHERE `type` = {$type} ORDER BY `edittime` DESC LIMIT {$from},{$limit}";
	$get = $db->query($sql);
	$res = $get->fetchAll(PDO::FETCH_OBJ);
	if ($res) {
		for ($i=0; $i < count($res); $i++) {
			$sql_u = "SELECT `realname` FROM `zx_user` WHERE `uid` = ".$res[$i]->editor;
			$sql_c = "SELECT `name` FROM `zx_category` WHERE `cid` = ".$res[$i]->type;
			$u = $db->query($sql_u)->fetch(PDO::FETCH_OBJ);
			$res[$i]->editor = $u->realname;
			$c = $db->query($sql_c)->fetch(PDO::FETCH_OBJ);
			$res[$i]->type = $c->name;
		}
		output($res);
	}else{
		return error("none");
	}
}

function getExamine($page,$limit,$type)
{
	$page = trim($page);
	$type = trim($type);
	if (!is_numeric($page) || !is_numeric($type)) {
		return error("invalid request");
	}
	$sql = "SELECT count(*) AS num FROM `zx_article` WHERE `type` = {$type} AND `examine` = 0";
	$db = dbMysql();
	$guide = $db->query($sql);
	$guide = $guide->fetch(PDO::FETCH_OBJ);
	$pageNum = ceil($guide->num / $limit);//总页数
	if (!is_numeric($page) || ($page > $pageNum) || ($page < 1)) {
		$page = 1;
	}
	$from = ($page-1) * $limit;
	$sql = "SELECT `aid`,`author`,`time`,`editor`,`from`,`type`,`realclick`,`title`,`reviewer`,`reviewtime` FROM `zx_article` WHERE `type` = {$type} AND `examine` = 0 ORDER BY `edittime` DESC LIMIT {$from},{$limit}";
	$get = $db->query($sql);
	$res = $get->fetchAll(PDO::FETCH_OBJ);
	if ($res) {
		for ($i=0; $i < count($res); $i++) {
			$sql_u = "SELECT `realname` FROM `zx_user` WHERE `uid` = ".$res[$i]->editor;
			$sql_e = "SELECT `realname` FROM `zx_user` WHERE `uid` = ".$res[$i]->reviewer;
			$sql_c = "SELECT `name` FROM `zx_category` WHERE `cid` = ".$res[$i]->type;
			$u = $db->query($sql_u)->fetch(PDO::FETCH_OBJ);
			$res[$i]->editor = $u->realname;
			$e = $db->query($sql_u)->fetch(PDO::FETCH_OBJ);
			$res[$i]->reviewer = $e->realname;
			$c = $db->query($sql_c)->fetch(PDO::FETCH_OBJ);
			$res[$i]->type = $c->name;
		}
		output($res);
	}else{
		return error("none");
	}
}

?>
<?php

define('GUIDE', 2);//user表中导员分类
define('GUIDENUM', 4);//斛兵导博每页导员数量
define('GUIDEBLOG', 5);//每位导员资讯数量

/************News*************/

//$page页数，$type: 资讯分类
function news($page,$limit,$type){
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
	$sql = "SELECT * FROM `zx_article` WHERE `type` = {$type} ORDER BY `time` DESC LIMIT {$from},{$limit}";
	$get = $db->query($sql);
	$res = $get->fetchAll(PDO::FETCH_OBJ);
	if ($res) {
		output($res);
	}else{
		return error("none");
	}
}

/***********Guide**********/
//返回导员ID、真实姓名、头像
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
	$pageNum = ceil($guide->num / GUIDENUM);//总页数
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


/*********Common************/
function dbMysql()//连接数据库
{
	$dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "dxsfzwljyzx";
    $dbname = "news";
    $db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->query("SET NAMES utf8");
    return $db;
}

function error($errMsg)//返回错误
{
	$err = array("status"=>"error","errmsg"=>$errMsg);
	output($err);
}

function output($arr)//输出
{
	echo json_encode($arr);
}
?>
<?php
require 'Slim/Slim.php';
$app = new Slim();

//For User
$app->get('/index/news','news');


// For Admin
$app->get('/admin/news','getAllNews');

$app->run();

function MysqlDb()
{
	$dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "news";
    $db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $db->query("set names utf8");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

function news()
{
}

function getAllNews()
{
	$sql = "SELECT * FROM `zx_article` ORDER BY `time` DESC LIMIT 1";
	$db = MysqlDb();
	$res = $db->query($sql);
	echo json_encode($res->fetchAll(PDO::FETCH_OBJ));
}
?>
<?php
require 'Slim/Slim.php';
require 'function.php';
$app = new Slim();
//index
$app->get("/news/:page/:limit/:type","news");

//导员博客
$app->get("/guideblog/getall/:page","getAllguide");

//管理员
$app->get("/admin/","");

$app->run();
?>
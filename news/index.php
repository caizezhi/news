<?php
require 'Slim/Slim.php';
require 'ucenter.php';
require 'function.php';
$app = new Slim();
//index
$app->get("/:page/:limit/:type","news");
$app->get("/:id","getNews");
$app->post("/subscribe","subscribe");

$app->get("/beta","beta");

//博客
$app->get("/guide/:page","getAllguide");
$app->get("/student/:page","getStunews");

//报告会
$app->get("/report/","getReport");

//管理员
$app->post("/admin/post","postNews");
$app->post("/admin/login","login");
$app->post("/admin/logout","logout");
$app->get("/admin/news/:page/:limit/:type","getAllnews");
$app->get("/admin/examine/:page/:limit/:type","getExamine");
$app->get("/admin/category","categories");

$app->run();
?>
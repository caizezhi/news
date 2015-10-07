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

//导员博客
$app->get("/guide/:page","getAllguide");

//报告会
$app->get("/report/:type","getReport");

//管理员
$app->post("/admin/login","login");
$app->post("/admin/logout","logout");

$app->run();
?>
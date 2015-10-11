<?php
session_start();
if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
	include 'login.html';
}else{
	@$action = key($_GET);
	switch ($action) {
		case 'allnews':
		    include 'allnews.html';
			break;
		case 'edit':
		    include 'edit.html';
			break;
		case 'examine':
		    include 'examine.html';
			break;
		default:
			include 'index.html';
			break;
	}
}

?>
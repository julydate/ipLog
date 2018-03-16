<?php 
define('INDEX',TRUE);
require_once('config.php');
$id = @$_GET['id'];
if(empty(@$_GET['id'])){exit('ID为空');}
if($id!='view'&&$id!='get'&&$id!='del'&&$id!='show'){exit('ID有误');}
if(empty(@$_GET['f'])&&@$_GET['id']!='show'){exit('文件名为空');}
//记录IP
if($id === 'view') {
	$f = @$_GET['f'];
	if(empty(@$_GET['t'])){exit('未传入时间参数');}
	$time = @$_GET['t'];
	if(time() - $time > "30"){exit('时间已过期');}
	$appkey = @$_GET['appkey'];
	$key = md5($f.$salt.$time);
	if($key === $appkey) {
		include('inc/viewip.php');
		$file = $f.".dat";
		$fp = fopen($file,"a");
		fputs($fp,$dat);
		//当文件大于1MB时进行删除，防D
		if(filesize($file) > 1048576) {
			unlink($file);
		}
	} else {
		exit('AppKey有误');
	}
}
//获取IP
if($id === 'get') {
	$f = @$_GET['f'];
	$appkey = @$_GET['appkey'];
	$key = md5($f.$salt);
	if($key === $appkey) {
	include('inc/get.php');
	} else {
		exit('AppKey有误');
	}
}
//删除IP
if($id === 'del') {
	$f = @$_GET['f'];
	$appkey = @$_GET['appkey'];
	$key = md5($f.$salt);
	if($key === $appkey) {
		echo "正在清空数据......";
		unlink($f.'.dat');
	} else {
		exit('AppKey有误');
	}
}
//查看所有IP
if($id === 'show') {
	$administrator = @$_GET['admin'];
	if($admin === $administrator) {
	include('inc/show.php');
	} else {
		exit('管理员私钥有误');
	}
}
?>
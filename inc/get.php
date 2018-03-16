<?php 
if(!defined('INDEX')) {
	exit('禁止访问'); 
}
$handle = @fopen("./".$f.".dat", "r");
$arr = array();
if ($handle) {
	while(!feof($handle)) {
		$item = fgets($handle, 4096);
		$arr[] = $item;
	}
	fclose($handle);
	header("Content-type:text/json;charset=utf-8");
	//print_r($arr);
	foreach ($arr as $temp) {  
		echo $temp;  
	} 
	} else {
		echo "文件错误！";
}
?>
<?php 
if(!defined('INDEX')) {
	exit('禁止访问'); 
}
if(@$_GET['clear'] === '1'){
	echo "正在清空数据......";
	if(file_exists("ip.dat")){unlink('ip.dat');}	
	header("Refresh:1;url=index.php?id=show&admin=".$admin);
	} else {
		$handle = @fopen("./ip.dat", "r");
		$arr = array();
		if ($handle) {
			while (!feof($handle)) {
				$item = fgets($handle, 4096);
				$arr[] = $item;
			}
			fclose($handle);
			header("Content-type:text/html;charset=utf-8");
			echo "<html><head></head><body>";
			echo "<pre>";
			//print_r($arr);
			foreach ($arr as $temp) {  
				echo $temp;  
			} 
			echo"</pre>";
			} else {
				echo "文件错误！";
		}
		echo '<button type="button" onclick="window.location.href=';
		echo "'index.php?id=show&clear=1&admin=".$admin."'";
		echo '">清空数据</button></body></html>';
}
?>
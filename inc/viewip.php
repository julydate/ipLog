<?php 
if(!defined('INDEX')) {
	exit('禁止访问'); 
}
//获取用户IP
if (getenv("HTTP_CLIENT_IP"))$ip = getenv("HTTP_CLIENT_IP");  
    else if(getenv("HTTP_X_FORWARDED_FOR"))$ip = getenv("HTTP_X_FORWARDED_FOR");  
    else if(getenv("REMOTE_ADDR"))$ip = getenv("REMOTE_ADDR");  
    else $ip = "Unknow";
//生成图像
$img_number = imagecreatefromjpeg('inc/bg.jpg');
$backcolor = imagecolorallocate($img_number,102,102,153);
$textcolor = imagecolorallocate($img_number,0,0,0);
imagefill($img_number,0,0,$backcolor);
$number = "$ip";
$TTFSize = ImageTTFBBox(20,0,'inc/sweetie.ttf',$number);
$TTFWidth = $TTFSize[2]-$TTFSize[0];
$TextX = (250-$TTFWidth)/2;
ImageTTFText ($img_number,20,0,$TextX,131,$textcolor,'inc/sweetie.ttf',$number);
header("Content-type: image/jpeg");
imagejpeg($img_number);
ImageDestroy($img_number);

//普通IP定位
//生成随机IP
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,"http://freeapi.ipip.net/?ip=".$ip);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_HEADER,0);
curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.2) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.2.149.27 ");
curl_setopt($ch, CURLOPT_TIMEOUT, 300);//设置超时限制防止死循环 
$getgps = curl_exec($ch);
$httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
if($getgps === FALSE||$httpCode >= "300" ){
	$gpsA = "N";
	} else {
		$gpsA = $getgps;
}
curl_close($ch);

//百度普通IP定位
//API控制台申请得到的ak（此处ak值仅供验证参考使用）
$ak = '';
//应用类型为for server, 请求校验方式为sn校验方式时，系统会自动生成sk，可以在应用配置-设置中选择Security Key显示进行查看（此处sk值仅供验证参考使用）
$sk = '';
//以Geocoding服务为例，地理编码的请求url，参数待填
$url = "http://api.map.baidu.com/location/ip?ip=%s&coor=%s&ak=%s&sn=%s";
//get请求uri前缀
$uri = '/location/ip';
//地理编码的请求中address参数
//$ip = $ip;
//地理编码的请求output参数
$coor = 'bd09ll';
//构造请求串数组
$querystring_arrays = array (
	'ip' => $ip,
	'coor' => $coor,
	'ak' => $ak
);
//调用sn计算函数，默认get请求
$sn = caculateAKSN($ak, $sk, $uri, $querystring_arrays);
//请求参数中有中文、特殊字符等需要进行urlencode，确保请求串与sn对应
$target = sprintf($url, urlencode($ip), $coor, $ak, $sn);
function caculateAKSN($ak, $sk, $url, $querystring_arrays, $method = 'GET')
{
    if ($method === 'POST'){  
        ksort($querystring_arrays);  
    }  
    $querystring = http_build_query($querystring_arrays);  
    return md5(urlencode($url.'?'.$querystring.$sk));  
}
$gpsB = @file_get_contents($target);
$gpsB = json_decode($gpsB,true);
$address = array_column($gpsB,'address');
$point = array_column($gpsB,'point');
$gpsx = array_column($point,'x');
$gpsy = array_column($point,'y');
if($address&&$gpsx&&$gpsy) {
	foreach ($address as $tempA){$addr = $tempA;}
	foreach ($gpsx as $tempB){$addrx = $tempB;}
	foreach ($gpsy as $tempC){$addry = $tempC;}
	$gpsB = $addr;
	/*$gpsB = $addr.",经度:".$addrx.",纬度:".$addry;*/
} else {
	$gpsB = "N";
}

//埃文科技高精度IP定位
$gpskey = "";//埃文科技定位ak
$iplocak = "";//百度开放API平台ak
include('iploc.php');
 
//组合定位
//$gps = $gpsA."-".$gpsB."-".$gpsC;
$gps = $gpsA."-".$gpsB."-".$gpsC;

//记录文件
$time = gmdate("H:i:s",time()+8*3600);
$timelong = gmdate("Y-m-d H:i:s",time()+8*3600);;
$file = "ip.dat" ;
$fp = fopen ("ip.dat","a")  ;
$dat= "$ip"."---"."$time"."---"."$gps"."\n";
$datlong = "$ip"."---"."$timelong"."---"."$gps"."\n";;
fputs($fp,$datlong);

//当文件大于100MB时进行删除，防止文件过大
if(filesize($file) > 104857600) {
	unlink($file);
}
?>
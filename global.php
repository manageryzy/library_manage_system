<?php
/************************************************************
*
*  a program just for homework
*
*					                 	by manageryzy
*										   2014.7.15
*
*************************************************************/

//this library comes from another project by myself.All right reserved for the other project


//防止用户直接调用这个公共的php，虽然应该不会有这样的漏洞
if(!isset($lib_included))die(0);


//some global settings
define('db_local_server','mysql:dbname=lib;host=127.0.0.1');	//本地数据库的地址
define('db_name','root');					//网站文章区等地方的数据库，本地数据库的数据库用户名
define('db_password','password');				//本地数据库的密码


//some global functions

//过滤用户输入,防止注入
function check_str($string, $isurl = false) 
{ 
$string = preg_replace('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/','',$string); 
$string = str_replace(array("\0","%00","\r"),'',$string); 
empty($isurl) && $string = preg_replace("/&(?!(#[0-9]+|[a-z]+);)/si",'&',$string); 
$string = str_replace(array("%3C",'<'),'<',$string); 
$string = str_replace(array("%3E",'>'),'>',$string); 
$string = str_replace(array('"',"'","\t",' '),array(' '," ",' ',' '),$string); 
return trim($string); 
} 


//过滤用户提交的页面文件啦
function uh($str)
{
$farr = array(
"/s+/", //过滤多余的空白
"/<(/?)(script|i?frame|style|html|body|title|link|meta|?|%)([^>]*?)>/isU", //过滤 <script 等可能引入恶意内容或恶意改变显示布局的代码,如果不需要插入flash等,还可以加入<object的过滤
"/(<[^>]*)on[a-zA-Z]+s*=([^>]*>)/isU", //过滤javascript的on事件
);
$tarr = array(
" ",
"<\1\2\3>", //如果要直接清除不安全的标签，这里可以留空
"\1\2",
);
$str = preg_replace( $farr,$tarr,$str);
return $str;
}

//获得用户ip
function GetIP(){
 if(!empty($_SERVER["HTTP_CLIENT_IP"])){
  $cip = $_SERVER["HTTP_CLIENT_IP"];
 }
 //防止像binota君一样伪造http头，这里就舍弃了吧
 /*elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
  $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
 }*/
 elseif(!empty($_SERVER["REMOTE_ADDR"])){
  $cip = $_SERVER["REMOTE_ADDR"];
 }
 else{
  $cip = "无法获取！";
 }
return $cip;
}

function show_chip($file)
{
	$fp=fopen($file,"r");
	if($file==NULL)
	{
		echo "内部错误，不能读取文件";
		exit(-1);
	}
	while(!feof($fp))
 	{
 		echo fgets($fp); //从模板中读入表单数据
 	}
	fclose($fp);
		
}

?>
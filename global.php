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


//��ֹ�û�ֱ�ӵ������������php����ȻӦ�ò�����������©��
if(!isset($lib_included))die(0);


//some global settings
define('db_local_server','mysql:dbname=lib;host=127.0.0.1');	//�������ݿ�ĵ�ַ
define('db_name','root');					//��վ�������ȵط������ݿ⣬�������ݿ�����ݿ��û���
define('db_password','password');				//�������ݿ������


//some global functions

//�����û�����,��ֹע��
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


//�����û��ύ��ҳ���ļ���
function uh($str)
{
$farr = array(
"/s+/", //���˶���Ŀհ�
"/<(/?)(script|i?frame|style|html|body|title|link|meta|?|%)([^>]*?)>/isU", //���� <script �ȿ�������������ݻ����ı���ʾ���ֵĴ���,�������Ҫ����flash��,�����Լ���<object�Ĺ���
"/(<[^>]*)on[a-zA-Z]+s*=([^>]*>)/isU", //����javascript��on�¼�
);
$tarr = array(
" ",
"<\1\2\3>", //���Ҫֱ���������ȫ�ı�ǩ�������������
"\1\2",
);
$str = preg_replace( $farr,$tarr,$str);
return $str;
}

//����û�ip
function GetIP(){
 if(!empty($_SERVER["HTTP_CLIENT_IP"])){
  $cip = $_SERVER["HTTP_CLIENT_IP"];
 }
 //��ֹ��binota��һ��α��httpͷ������������˰�
 /*elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
  $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
 }*/
 elseif(!empty($_SERVER["REMOTE_ADDR"])){
  $cip = $_SERVER["REMOTE_ADDR"];
 }
 else{
  $cip = "�޷���ȡ��";
 }
return $cip;
}

function show_chip($file)
{
	$fp=fopen($file,"r");
	if($file==NULL)
	{
		echo "�ڲ����󣬲��ܶ�ȡ�ļ�";
		exit(-1);
	}
	while(!feof($fp))
 	{
 		echo fgets($fp); //��ģ���ж��������
 	}
	fclose($fp);
		
}

?>
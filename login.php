<?php
/************************************************************
*
*  a program just for homework
*
*					                 	by manageryzy
*										   2014.7.15
*
*************************************************************/
//require the global lib
$lib_included=1;
require_once('global.php');

session_start();

//get and clean the user input to defence from SQL injet
$name=check_str($_POST['name']);
$pwd=check_str($_POST['pwd']);

if($name!=$_POST['name']||$pwd!=$_POST['pwd'])
{
	echo '<h1>please do not try to attack me!</h1>';
	die(0);
}

//lead the strange user to the login page
if($name==""||$pwd=="")
{
	header( "Location: login.html");
	die(0);
}


//check whether the user could login
$locCon;
try 
{
	$locCon=new PDO(db_local_server,db_name,db_password,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
}
catch (PDOException $e) 
{
	header('HTTP/1.1 500 Internal Server Error');  
	header('status: 500 Internal Server Error');
	die();
}

$res=$locCon->query("SELECT * FROM lib_user WHERE username = '$name' and `password` = PASSWORD('$pwd')")->fetch();

if($res)
{
	$_SESSION['uid']=$res['userid'];
	$_SESSION['name']=$res['username'];
	header( "Location: index.php");
}
else
{
	header( "Location: login.html#error");
}

?>
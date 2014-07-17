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
if(!isset($_SESSION['uid']))
{
	header( "Location: login.html");
	die(0);
}


$do='home';
if(isset($_REQUEST['do']))
{
	$do=$_REQUEST['do'];
	if($do!='home'&&$do!='search_book_by_name'&&$do!='return'&&$do!='borrow')
	{
		header( "Location: index.php");
	}
}

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

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>library manager</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="navbar-fixed-top.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="js/ie-emulation-modes-warning.js"></script>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <!-- Fixed navbar -->
    <div class="navbar navbar-default navbar-fixed-top" role="navigation" id="menu">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"></button>
          <a class="navbar-brand" href="index.php">Lib manage</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li id="home_li"><a href="#">Main</a></li>
            <li id="about_li"><a href="#about">About</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li id="search_li"><a href="#search">Search</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>

    <div class="container" id="main">
	  <div id="menu_hidden"><!--take the place of the menu --></div>
      <!-- Main component for a primary marketing message or call to action -->
      <?php
	  if($do=='home')
	  {
		  $isfound=0;
		  echo '<h3>My Books</h2>';
		  show_chip('book_table.chip.html');
		  $sql='SELECT * FROM lib_book,lib_borrow WHERE lib_borrow.uid = '.$_SESSION['uid'].' AND lib_book.bookId=lib_borrow.bookId;';
		  foreach ($locCon->query($sql) as $row) 
		  {
			$isfound=1;
			echo '<tr>';
			echo '<td><img class="bookImg" src="'.$row['img'].'" ></img></td>';
			echo '<td>'.$row['bookName'].'</td>';
			echo '<td>'.$row['abstract'].'</td>';
			echo '<td>'.$row['mount'].'</td>';
			echo '<td>'.$row['count'].'</td>';
			echo '<td><a href="index.php?do=return&bid='.$row['bookId'].'">return</a><td>';
  			if ($locCon->errorCode() != '00000')
			{
				echo '<h1>Error 500</h1>';
			}
		   }
		   echo '</table>';
		   if($isfound==0)
		   {
			   echo '<br/><br/><h1>nothing was found!Go to search and borrow some books!</h1>';
		   }
	  }
	  else if($do=='search_book_by_name')
	  {
		  $isfound=0;
		  echo '<h3>Search Result</h2>';
		  show_chip('book_table.chip.html');
		  
		  $search=check_str($_REQUEST['bookname']);
		  
		  $sql='SELECT * FROM lib_book WHERE lib_book.bookName like \'%'.$search.'%\';';
		  foreach ($locCon->query($sql) as $row) 
		  {
			$isfound=1;
			echo '<tr>';
			echo '<td><img class="bookImg" src="'.$row['img'].'" ></img></td>';
			echo '<td>'.$row['bookName'].'</td>';
			echo '<td>'.$row['abstract'].'</td>';
			echo '<td>'.$row['mount'].'</td>';
			echo '<td>'.$row['count'].'</td>';
			echo '<td><a href="index.php?do=borrow&bid='.$row['bookId'].'">borrow</a><td>';
  			if ($locCon->errorCode() != '00000')
			{
				echo '<h1>Error 500</h1>';
			}
		   }
		   echo '</table>';
		   if($isfound==0)
		   {
			   echo '<br/><br/><h1>nothing was found! Go to search some other!</h1>';
		   }
	  }
	  else if($do=='borrow')
	  {
		  $uid=$_SESSION['uid'];
		  if(!isset($_REQUEST['bid']))
		  {
			  echo 'Error: some important parmeters are missing!';
			  die(0);
		  }
		  $bid=(int)$_REQUEST['bid'];
		  
		  $res=$locCon->query("SELECT * FROM lib_borrow WHERE uid = '$uid' and `bookId` = $bid")->fetch();
		  
		  if($res)
		  {
			  echo '<h1>you have already own this book!</h1>';
		  }
		  else
		  {
			  $res=$locCon->query("SELECT * FROM lib_book WHERE `bookId` = $bid")->fetch();
			  if((int)$res['count']<=0)
			  {
				  echo '<h1>more books are needed</h1>';
			  }
			  else
			  {

			  	try
			  	{
			  		$locCon->exec("INSERT INTO lib_borrow VALUES ($uid,$bid);");
			  		$locCon->exec("INSERT INTO lib_log (uid,bookId,`time`,`event`) VALUES ($uid,$bid,CURRENT_TIMESTAMP ,1);");
			  		$locCon->exec("UPDATE lib_user SET borrow = borrow+1 WHERE userid = $uid;");
			  		$locCon->exec("UPDATE lib_book SET count = count-1 WHERE bookId = $bid;");
			  	}
			  	catch(Exception $e) 
			  	{
					  echo '<h1>an very serious problem was found when dealing with the database! please tell the administrator to check data !</h1>';
					  die(0);
			  	}
			  	echo '<h1><a href="index.php">Succeed,Click me to the home page.</a></h1>';
			  }
		  }

	  }
	  else if($do=='return')
	  {
		  $uid=$_SESSION['uid'];
		  if(!isset($_REQUEST['bid']))
		  {
			  echo 'Error: some important parmeters are missing!';
			  die(0);
		  }
		  $bid=(int)$_REQUEST['bid'];
		  
		  $res=$locCon->query("SELECT * FROM lib_borrow WHERE uid = '$uid' and `bookId` = $bid")->fetch();
		  
		  if($res)
		  {
			  try
			  {
			  	$locCon->exec("DELETE FROM lib_borrow WHERE uid = $uid AND bookId = $bid;");
			  	$locCon->exec("INSERT INTO lib_log (uid,bookId,`time`,`event`) VALUES ($uid,$bid,CURRENT_TIMESTAMP ,0);");
			  	$locCon->exec("UPDATE lib_user SET borrow = borrow-1 WHERE userid = $uid;");
			  	$locCon->exec("UPDATE lib_book SET count = count+1 WHERE bookId = $bid;");
			  }
			  catch(Exception $e) 
			  {
				  echo '<h1>an very serious problem was found when dealing with the database! please tell the administrator to check data !</h1>';
				  die(0);
			  }
		  }
		  else
		  {
			  echo '<h1>you have not own this book yet!</h1>';
		  }
		  echo '<h1><a href="index.php">Succeed,Click me to the home page.</a></h1>';
	  }
	  ?>
    </div> <!-- /container -->

	
    
    <div class="jumbotron" name="about" id="about">
        <h1>lib manage system example</h1>
        <p>This example is a quick exercise (really very quick ,just in a day... ) to show how to make a manage system based on Apache Httped,PHP and MySQL.</p>
        <p>For the time reason I did not use ajax or other tech to get better perform.So if you want to use this demo as a part of your project , I recommand you add the ajax function to get better perform.</p>
        <p>Apache Httped ,PHP ,MySQL ,bootstrap and jQuery were used in this demo. Thanks to Apache,PHP and MySQL Community , and thanks to Twitter and Google and other Inc who have contributed to those open source projects who is this base of this demo.</p>
        <p>Special thanks to <a href="http://Glyphicons.com">Glyphicons</a> for providing free icon to Bootstrap. And thanks for <a href="http://hmacg.cn">hmoe ACG</a> for providing some picture as test picture. </p>
        <p>Copyright &copy; manageryzy 2014 , under the <a href="http://mit-license.org/" target="new" >MIT Licence</a> . </p>
        <br/>
        <p>
           <a class="btn btn-lg btn-primary" href="http://github.com/manageryzy/" target="new" role="button">View my page &raquo;</a>
    	</p>
    </div>
    
    <div style="height:100%" class="jumbotron" name="search" id="search">
    	<div class="container ">
            <form class="form-signin center-block" role="form" action="index.php?do=search_book_by_name" method="post" style="width:40%;">
      			<h2 class="form-signin-heading">Search the books!</h2>
        		<input name="bookname" type="bookname" class="form-control" placeholder="Book name" required autofocus><br/>
        		<button class="btn btn-lg btn-primary btn-block" type="submit"><span class="glyphicon-search"></span>Search</button> 
      		</form>
        </div>
    </div>
      
      
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery-2.1.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script >
	//the callback fucntion of the hash change event
	function hashChangeFire()
	{
		if(window.location.hash=="#about")
		{
			$('#about_li').addClass('active');
			$('#about').show();
		}
		else 
		{
			$('#about_li').removeClass('active');
			$('#about').hide();
		}
		
		if(window.location.hash=="#search")
		{
			$('#search_li').addClass('active');
			$('#search').show();
		}
		else 
		{
			$('#search_li').removeClass('active');
			$('#search').hide();
		}
		
		if(window.location.hash=="")
		{
			$('#home_li').addClass('active');
			$('#main').show();
		}
		else
		{
			$('#home_li').removeClass('active');
			$('#main').hide();
		}
	}
	hashChangeFire();
	
	
	
	//these code came from http://www.impng.com/web-dev/hashchange-event-and-onhashchange.html to detect hash change event
	if( ('onhashchange' in window) && ((typeof document.documentMode==='undefined') || document.documentMode==8)) 
	{
    	// 浏览器支持onhashchange事件
    	window.onhashchange = hashChangeFire;  // TODO，对应新的hash执行的操作函数
	} 
	else {
   	 	// 不支持则用定时器检测的办法
    	setInterval(function() 
		{
        	var ischanged = isHashChanged();  // TODO，检测hash值或其中某一段是否更改的函数
        	if(ischanged) 
			{
            	hashChangeFire();  // TODO，对应新的hash执行的操作函数
       		}
    	}, 150);
	}
	
	$('#menu_hidden').height($('#menu').height());
	</script>
  </body>
</html>

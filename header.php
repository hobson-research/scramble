<?php
	require('protect.php');
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
	
<?php

    function __autoload($className)
    {  
        include_once 'includes/class.' . $className . '.php';  
    }
		
	$db = new db("mysql:host=localhost;dbname=", "", "");
	
	$zOutput = new zOutputHandler;
?>

	<!-- Basic Page Needs
	================================================== -->
	<meta charset="utf-8">
	<title>Ztree (Un)scramble Helper</title>
	<meta name="description" content="A simple PHP application to help generate ztree code">
	<meta name="author" content="Ye Joo Park">
	
	<!-- Mobile Specific Metas
	================================================== -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	
	<!-- CSS
	================================================== -->
	<link rel="stylesheet" href="css/presets.css">
	<link rel="stylesheet" href="css/skeleton.css">
	<link rel="stylesheet" href="css/style.css">
	
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	
	<!-- Favicons
	================================================== -->
	<link rel="shortcut icon" href="images/favicon.ico">
	
	<!-- Typekit
	================================================== -->
	<script type="text/javascript" src="//use.typekit.net/ulw2hye.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
</head>
<body>
	

	<!-- Header
	================================================== -->
	<div id="section-header">
		<div class="container">
			<div class="eight columns">
				<h1><a href="index.php" title="Home">Ztree / <span style="color: #ddd; ">Unscramble</span></a></h1>
			</div><!-- // .eight -->
			
			<div class="eight columns">
				<a href="protect.php?signout=1" class="logout" style="display: none; ">Sign Out</a>
			</div>
		</div><!-- // .container -->
	</div><!-- // #section-header -->
	
	<!-- Menu
	================================================== -->
	<div id="section-menu">
		<div class="container">
			<div class="sixteen columns">
				<ul class="nav">
					<li><a href="batchFilter.php">Filter Summary</a></li>
					<li><a href="index.php">Words List</a></li>
					<li><a href="scramble.php">Scramble</a></li>
					<li><a href="viewzCode.php">z-Tree Code</a></li>
					<li><a href="check.php">Anagram Check</a></li>
				</ul>
			</div><!-- // .sixteen -->
		</div><!-- // .container -->
	</div><!-- // #section-menu -->
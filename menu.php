<?php
	include("include.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<title>新聞推薦系統行動版</title> 
		<meta charset="utf-8">
		<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, width=device-width">
		
		<link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css" />
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
		<script src="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.js"></script>
	</head>
	<body>
	<div data-role="page" id="menu">
		<div data-role="header">
			<h3>首頁 - 新聞推薦系統</h3>
			<a href="jre.html" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
			<div data-role="navbar" data-theme="b" >
				<ul>
					<li><a data-theme="b" href="list.html" data-icon="grid">新聞列表</a></li>
					<li><a data-theme="b" href="recommendation.html" data-icon="search">新聞推薦</a></li>
					<li><a data-theme="b" href="history.html" data-icon="forward">歷史回顧</a></li>
				</ul>
			</div>
		</div>
		<div data-role="content">
			<p>登入成功</p>
		</div>
		<div data-role="footer">
			<h4>新聞推薦組-組員：戴均民、郭書佑、朱奕安</h4>
		</div><!-- /footer -->
	</div>	
	</body>
</html>

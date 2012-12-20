<?php include_once("include.php"); ?>
<!DOCTYPE html>
<html>
	<head>
		<title>新聞推薦系統行動版</title> 
		<meta charset="utf-8" />
		<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, width=device-width" />
		
		<link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css" />
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
		<script src="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.js"></script>
		<script src="js/jquery.validate.min.js"></script>
		<script src="script.js"></script>
		<script src="js/messages_zh_TW.js"></script>
<?php
	$pagenav = <<<navSTR
			<div data-role="navbar" data-id="nav" >
				<ul>
					<li><a data-theme="b" href="index.php">功能選單</a></li>
					<li><a data-theme="b" href="">今日推薦</a></li>
					<li><a data-theme="b" href="profile.php" data-ajax="false">個人資料</a></li>
					<li><a data-theme="b" href="setting.php">設定</a></li>
				</ul>
			</div>
navSTR;
?>
	</head>
	<body>
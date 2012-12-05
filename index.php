<?php
	include_once("include.php");
	if( $_GET['act']=='logout' )
	{
		unset($ses->uid);
		tai_location();
	}
	if( isset( $_POST['submit'] ))
	{
		$errCnt = 0;
		if( $errCnt==0 && !$dck->email($_POST['email']) )
		{	$ses->msg('錯誤：帳號不存在或密碼有誤'); $errCnt++;	}
		if($errCnt == 0)
		{
			$_POST['email'] = @mysql_escape_string($_POST['email']);
			$sql = "select * from `user` where `email`='{$_POST['email']}' limit 1";
			$lgnRes = mysql_query($sql);
			if( ! ($lgnRow = mysql_fetch_assoc($lgnRes)) )
			{	$ses->msg('錯誤：帳號不存在或密碼有誤'); $errCnt++;	}
			@mysql_free_result($lgnRes);
		}
		if( $errCnt == 0 && $lgnRow['pass'] != sha1($_POST['pass']))
		{	$ses->msg('錯誤：帳號不存在或密碼有誤'); $errCnt++;	}
		if( $errCnt == 0 )	// 登入成功
		{
			$ses->uid = $lgnRow['uid'];
			$sql = "update `user` set `login_t`=now() where `uid`='{$ses->uid}'";
			tai_mysqlExec($sql);
		}
		else tai_location('index.php');
	}
	include_once("header.php");
	
if( !$ses->uid ) { // 未登入，顯示登入的表單。
?>
	<div data-role="page" id="login" data-add-back-btn="true">
		<div data-role="header" data-position="fixed">
			<h1>登入 - 新聞推薦系統</h1>
			<a href="index.php" data-icon="home" data-iconpos="notext" data-direction="reverse" class="ui-btn-right">Home</a>
		</div>
		<div data-role="content">
			<form method="post" action="index.php" data-ajax="false">
				<label for="name">使用者名稱</label>
				<input type="email" name="email" value="" tabindex="1" />
				<label for="pass">密碼</label>
				<input type="password" name="pass" value="" tabindex="2" />
				<div class="ui-grid-a">
					<div class="ui-block-a">
						<a href="register.php" data-role="button" data-theme="d" data-ajax="false" >註冊</a>
					</div>
					<div class="ui-block-b">
						<input type="submit" name="submit" value="登入" data-theme="e" />
					</div>
				</div>
			</form>
		</div>
<?php
}
else	// 已登入，顯示功能選單
{ 
?>
	<div data-role="page" id="menu">
		<div data-role="header" data-position="fixed">
			<h1>選單 - 新聞推薦系統</h1>
			<a href="index.php?act=logout" data-icon="alert" data-direction="reverse" class="ui-btn-right" data-ajax="false">登出</a>
<?php echo $pagenav; ?>
		</div>
		<div data-role="content">
			<p><?php echo tai_dbUser('name'); ?>，歡迎回來</p>
			<div class="ui-grid-b">
				<div class="ui-block-a">
					<a data-role="button" href="listNews.php?act=groupByDate">
						依日期<br />看新聞
					</a>
				</div>
				<div class="ui-block-b">
					<a data-role="button" href="listNews.php?act=groupByCategory">
						依分類<br />看新聞
					</a>
				</div>
				<div class="ui-block-c">
					<a data-role="button" href="recommand1.php">
						推薦1<br />測試
					</a>
				</div>
				<div class="ui-block-a">
					<a data-role="button" href="word.php">
						關鍵字<br />排行
					</a>
				</div>
			</div>
		</div>
<?php
}
include_once('footer.php');
?>

<?php
	include_once("include.php");
	if( $ses->uid )
		tai_location('index.php');
	if( isset($_POST['submit']) )
	{
		$errCnt = 0;
		$post = array();
		foreach( array('email','pass','pass2','name','birth','gender') as $field )
		{
			if( strlen($_POST[$field]) == 0 )
			{
				$errCnt++;
				$ses->msg("錯誤：$field 欄位沒有填寫");
			}
			$post[$field] = @mysql_escape_string($_POST[$field]);
		}
		if($errCnt==0 && !$dck->email($_POST['email']) )
		{
			$errCnt++;
			$ses->msg("錯誤：請填寫正確的電子郵件。");
		}
		if($errCnt==0 && $_POST['pass']!=$_POST['pass2'] )
		{
			$errCnt++;
			$ses->msg("錯誤：您輸入的兩次密碼不一樣。");
		}
		if($errCnt==0 && !$dck->date($_POST['birth']) )
		{
			$errCnt++;
			$ses->msg("錯誤：請填寫正確的生日。");
		}
		if($errCnt==0)
		{
			$sql = "select count(*) as 'cnt' from `user` where `email`='{$_POST['email']}'";
			$userCnt = mysql_fetch_row(mysql_query($sql));
			if($userCnt[0]!='0')
			{
				$errCnt++;
				$ses->msg("錯誤：這個 email 已經申請過了，如果忘記密碼，請聯絡管理員。");
			}
		}
		if($errCnt==0)
		{
			unset($post['pass2']);
			$post['pass'] = sha1($post['pass']);
			$sql = "insert into `user` (`".implode('`,`',array_keys($post))."`) values ('".implode("','",$post)."')";
			tai_mysqlExec($sql);
			if(empty($_GET['app']))
				tai_location('index.php');
			else die('alert("感謝您的註冊，請按回到應用程式登入。")');
		}
	}
	include_once("header.php");
?>
	<div data-role="page" id="register" class="type-interior" data-add-back-btn="true">
		<div data-role="header" data-position="fixed">
			<h1>註冊 - 新聞推薦系統</h1>
			<a href="index.php" data-icon="home" data-direction="reverse" class="ui-btn-right" data-ajax="false">登入</a>
		</div>
		<div data-role="content">
			<?php
				if($ses->hasMsg())
				{
					echo '<script>alert(\''. $ses->msg() .'\');</script>';
					$ses->clearMsg();
				}
			?>
			<style>
				label.error {
						color: red;
						font-size: 16px;
						font-weight: normal;
						line-height: 1.4;
						margin-top: 0.5em;
						width: 100%;
						float: none;
				}

				@media screen and (orientation: portrait){
						label.error { margin-left: 0; display: block; }
				}

				@media screen and (orientation: landscape){
						label.error { display: inline-block; margin-left: 22%; }
				}

				em { color: red; font-weight: bold; padding-right: .25em; }
			</style>
			<form action="register.php<?php if($_GET['app']==1) echo '?app=1'; ?>" method="post" id="form_register" data-ajax="false">
				<label for="email">電子郵件</label>
				<input type="email" name="email" id="email" value="<?php echo $_POST['email']; ?>" />
				<label for="pass">密碼</label>
				<input type="password" name="pass" id="pass" value="" />
				<label for="pass2">確認密碼</label>
				<input type="password" name="pass2" id="pass2" value="" />
				<label for="name">姓名</label>
				<input type="text" name="name" id="name" value="<?php echo $_POST['name']; ?>" />
				<label for="birth">生日</label>
				<input type="date" name="birth" id="birth" value="<?php echo $_POST['birth']; ?>" />
				<div class="ui-grid-a">
					<div class="ui-block-a">性別</div>
					<div class="ui-block-b">
						<div data-role="controlgroup" data-type="horizontal">
							<label><input type="radio" name="gender" value="男" checked="checked" />男</label>
							<label><input type="radio" name="gender" value="女" />女</label>
						</div>
					</div>
				</div>
				<div class="ui-grid-a">
					<div class="ui-block-a"><input type="reset" value="重填" /></div>
					<div class="ui-block-b"><input type="submit" name="submit" value="確認"></div>
				</div>
			</form>
		</div>
<?php
include_once('footer.php');
?>
 
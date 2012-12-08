<?php
	include_once("include.php");
	include_once("header.php");
	if(!$ses->uid) tai_location('index.php');
	if(isset($_POST['submit']))
	{
		$errCnt = 0;
		
		if( tai_dbUser('pass') != sha1($_POST['pass']))
		{ $ses->msg('密碼錯誤，請確認'); $errCnt++; }
		else unset($_POST['pass']);
		
		if( empty($_POST['pass1']) )
		{
			unset($_POST['pass1']);
			unset($_POST['pass2']);
		}
		else
		{
			if( $_POST['pass1']!=$_POST['pass2'] )
			{ $ses->msg('兩次新密碼不同，請確認'); $errCnt++; }
			else $_POST['pass'] = sha1($_POST['pass1']);
		}
		
		if( $errCnt==0 )
		{
			$acpf = array('pass','name','gender','birth');
			$post = array();
			foreach( $acpf as $k )
				if(isset($_POST[$k])) $post[$k] = " `$k` = '".@mysql_escape_string($_POST[$k])."' ";
			$sql = "update `user` set ".implode(',',$post)." where `uid`='{$ses->uid}'";
			tai_mysqlExec($sql);
			$ses->msg('修改成功');
			unset($_POST);
			unset($post);
			tai_location('profile.php');
		}
	}
	else
	{
		$sql = "select * from `user` where `uid`='{$ses->uid}'";
		$userRes = mysql_query($sql);
		$_POST = mysql_fetch_assoc($userRes);
		@mysql_free_result($userRes);
	}
?>
	<div data-role="page" id="profile" data-add-back-btn="true">
		<div data-role="header" data-position="fixed">
			<h1>修改資料</h1>
			<a href="index.php?act=logout" data-icon="alert" data-direction="reverse" class="ui-btn-right" data-ajax="false">登出</a>
<?php echo $pagenav; ?>
		</div>
		<div data-role="content">
			<?php
				if($ses->hasMsg())
				{
					echo '<script>alert(\''. $ses->msg() .'\');</script>';
					$ses->clearMsg();
				}
			?>
			<script>
			$('#profile').bind('pageinit', function(event) {
				$('#form_modify').validate({
					rules: {
						pass: "required",
						pass2: {
							equalTo: "input[name=pass1]"
						},
						name: "required",
						birth: "required dateISO",
						gender: "required"
					}
				});
			});
			</script>
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
			<form action="profile.php" method="post" id="form_modify" data-ajax="false">
				<label>電子郵件<input type="email" name="email" value="<?=tai_dbUser('email')?>" disabled="disabled" /></label>
				<label>目前密碼<input type="password" name="pass" value="" placeholder="必須填寫密碼才可修改資料" /></label>
				<label>新密碼<input type="password" name="pass1" value="" placeholder="若不修改密碼此欄請留空" /></label>
				<label>新密碼確認<input type="password" name="pass2" value="" placeholder="若不修改密碼此欄請留空" /></label>
				<label>姓名<input type="text" name="name" value="<?=$_POST['name']?>" /></label>
				<label>生日<input type="date" name="birth" value="<?=$_POST['birth']?>" /></label>
				<div class="ui-grid-a">
					<div class="ui-block-a">性別</div>
					<div class="ui-block-b">
						<div data-role="controlgroup" data-type="horizontal">
							<label><input type="radio" name="gender" value="男" <?=($_POST['gender']=='男')?'checked="checked" ':''?>/>男</label>
							<label><input type="radio" name="gender" value="女" <?=($_POST['gender']=='女')?'checked="checked" ':''?>/>女</label>
						</div>
					</div>
				</div>
				<div class="ui-grid-a">
					<div class="ui-block-a"><a href="index.php" data-role="button" data-ajax="false">回功能選單</a></div>
					<div class="ui-block-b"><input type="submit" name="submit" value="修改"></div>
				</div>
			</form>
		</div>
<?php
include_once('footer.php');
?>

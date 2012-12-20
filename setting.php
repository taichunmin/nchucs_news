<?php
	include_once("include.php");
	include_once("header.php");
	if(!$ses->uid) tai_location('index.php');
	if(isset($_POST['submit'])) $stt->post();
?>
	<div data-role="page" id="setting" data-add-back-btn="true">
		<div data-role="header" data-position="fixed">
			<h1>設定</h1>
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
			<form action="" method="post"><div data-role="collapsible-set">
				<div data-role="collapsible" data-content-theme="d" data-theme="b">
					<input type="range" name="simi_1st" value="60" min="0" max="100" data-highlight="true"  />
					<input type="range" name="simi_2st" value="30" min="0" max="100" data-highlight="true"  />
					<input type="range" name="simi_3st" value="10" min="0" max="100" data-highlight="true"  />
				</div>
			</div></form>
<?php
//==================================================================================================
//==================================================================================================
?>
		</div>
<?php
include_once('footer.php');
?>

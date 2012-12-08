<?php
	// insert into `viewlog` select NULL, 6, `nid`, now() from `news` where `rid` = 31 limit 20
	include_once("include.php");
	include_once("header.php");
	if(!$ses->uid) tai_location('index.php');
	
	$cfg['sevenDay'] = date('Y-m-d',time()-7*24*60*60);
	
	function tai_viewlogByUid()
	{
		global $cfg,$ses;
		$sql = "select * from `viewlog` where `uid`='{$ses->uid}' and `view_t`>'{$cfg['sevenDay']}'";
		$res = mysql_query($sql);
		$view = array();
		while( $row = mysql_fetch_assoc($res) )
			$view[] = $row['nid'];
		@mysql_free_result($res);
		return $view;
	}
?>
	<div data-role="page" id="-<?php echo $_GET['act']; ?>" data-add-back-btn="true">
	<style>
		table.viewTab{ width: 100%; border-collapse: collapse; }
		table.viewTab th, table.viewTab td{ border: 1px solid #000; }
	</style>
		<div data-role="header" data-position="fixed">
			<h1>推薦測試</h1>
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
<?php
//==================================================================================================
?>
<div data-role="collapsible-set">
	<div data-role="collapsible" data-content-theme="d" data-theme="b">
		<h3>取得目前的登入資訊</h3>
		<p>目前以 <?php echo tai_dbUser('name'); ?> (UID: <?php echo $ses->uid; ?>) 登入。</p>
	</div>
<?php
	// 取得瀏覽紀錄
	$viewlog = tai_viewlogByUid();
	$viewlogSQL = implode(',',$viewlog);
?>
	<div data-role="collapsible" data-content-theme="d" data-theme="b">
		<h3>取得瀏覽紀錄</h3>
		<p>您擁有 <?php echo count($viewlog); ?> 筆瀏覽紀錄。</p>
	</div>
<?php
	// 取得 瀏覽紀錄 分布的分類
	$nidbyrid = array();
	if($viewlogSQL != '')
	{
		$sql = "select `rid`,`nid` from `news` where `nid` in ($viewlogSQL)";
		$ridRes = mysql_query($sql);
		while( $ridRow = mysql_fetch_assoc($ridRes))
			$nidbyrid[ $ridRow['rid'] ][] = $ridRow['nid'];
?>
	<div data-role="collapsible" data-content-theme="d" data-theme="b">
		<h3>取得 瀏覽紀錄 分布的分類</h3>
		<p>在七天內瀏覽過 <?php echo count($nidbyrid); ?> 個分類。</p>
		<table class="viewTab">
			<tr><th>分類ID</th><th>閱讀數量</th></tr>
		<?php 
		foreach($nidbyrid as $k => $v)
			echo '<tr><td>'.$k.'</td><td>'.count($v).'</td></tr>';
		?>
		</table>
	</div>
<?php } ?>
</div>
<?php
//==================================================================================================
?>
		</div>
<?php
include_once('footer.php');
?>

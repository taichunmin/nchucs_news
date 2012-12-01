<?php
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
		<div data-role="header" data-position="fixed">
			<h1>推薦測試</h1>
			<a href="index.php?act=logout" data-icon="alert" data-direction="reverse" class="ui-btn-right" data-ajax="false">登出</a>
<?php echo $pagenav; ?>
		</div>
		<div data-role="content">
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
	$viewlogSQL = (( count($viewlog)>0 )?implode(',',$viewlog):'');
?>
	<div data-role="collapsible" data-content-theme="d" data-theme="b">
		<h3>取得瀏覽紀錄</h3>
		<p>在七天內共有 <?php echo count($viewlog); ?> 筆瀏覽紀錄。</p>
		<p><?php echo $viewlogSQL; ?></p>
	</div>
<?php
	// 取得 瀏覽紀錄 分布的分類
	if($viewlogSQL != '')
	{
		$sql = "select `rid`,count(`nid`) as 'cnt' from `news` where `nid` in ($viewlogSQL) group by `rid` ";
		$ridRes = mysql_query($sql);
		$category = array();
		while( $ridRow = mysql_fetch_assoc($ridRes))
			$category[ $ridRow['rid'] ] = intval($ridRow['cnt']);
?>
	<div data-role="collapsible" data-content-theme="d" data-theme="b">
		<h3>取得 瀏覽紀錄 分布的分類</h3>
		<p>在七天內瀏覽過 <?php echo count($category); ?> 個分類。</p>
		<p><?php var_export($category); ?></p>
	</div>
</div>
<?php
	}
//==================================================================================================
?>
		</div>
<?php
include_once('footer.php');
?>

<?php
	// insert into `viewlog` select NULL, 6, `nid`, now() from `news` where `rid` = 31 limit 20
	include_once("include.php");
	include_once("header.php");
	if(!$ses->uid) tai_location('index.php');
	
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
	<div data-role="page" id="ontology" data-add-back-btn="true">
	<style>
		table.viewTab{ width: 100%; border-collapse: collapse; }
		table.viewTab th, table.viewTab td{ border: 1px solid #000; }
	</style>
		<div data-role="header" data-position="fixed">
			<h1>個人化推薦展示</h1>
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
if(isset($_GET['uid']))
	$uid = $_GET['uid'];
else $uid = $ses->uid;
?>
<div data-role="collapsible-set">
	<style>
		table.viewTab{ width: 100%; border-collapse: collapse; }
		table.viewTab th, table.viewTab td{ border: 1px solid #000; }
	</style>
	<div data-role="collapsible" data-collapsed="false" data-content-theme="d" data-theme="b">
		<h3>取得目前的登入資訊</h3>
		<p>目前以 <?php echo tai_dbUser('name',NULL,$uid); ?> (UID: <?php echo $uid; ?>) 登入。</p>
	</div>
<?php
	// 取得推薦統計
	$sql = "select `uid`,count(`nid`) as 'cnt' from `ontology` group by `uid` limit 50";
	$analyRes = mysql_query($sql);
?>
	<div data-role="collapsible" data-content-theme="d" data-theme="b">
		<h3>個人化推薦統計</h3>
		<table class="viewTab">
			<tr>
				<th>使用者</th>
				<th>推薦數量 (最大 100)</th>
			</tr>
		<?php while($analyRow = mysql_fetch_assoc($analyRes)) { ?>
			<tr>
				<th><a href="?uid=<?=$analyRow['uid']?>"><?=tai_dbUser('name',NULL,$analyRow['uid'])?></th>
				<th><?=$analyRow['cnt']?></th>
			</tr>
		<?php } mysql_free_result($analyRes); ?>
		</table>
		<p>註：個人化推薦並非即時更新，若要更新<a href="ontology.php?date=<?=$cfg['yesterday']?>" target="ontologyUpdate" data-role="button" data-icon="refresh" data-inline="true" data-mini="true" onclick="alert('更新成功後會自動重新整理'); $('#ontologyUpdate').one('load',function(){ history.go(0); });" >請按此</a><iframe style="width:0; height:0" id="ontologyUpdate" ></iframe></p>
	</div>
<?php
	// 取得 推薦紀錄
	$sql = "select * from `ontology` where `uid` = '$uid' order by `weight` desc";
	$ontoRes = mysql_query($sql);
	$ontology = array();
?>
	<div data-role="collapsible" data-content-theme="d" data-theme="b">
		<h3>個人化推薦詳細資料</h3>
		<table class="viewTab">
			<tr>
				<th>新聞 ID</th>
				<th>推薦權重</th>
			</tr>
		<?php 
		while($ontoRow = mysql_fetch_assoc($ontoRes))
		{
			$ontology[] = $ontoRow['nid'];
		?>
			<tr>
				<th><?=$ontoRow['nid']?></th>
				<th><?=$ontoRow['weight']?></th>
			</tr>
		<?php } mysql_free_result($ontoRes); ?>
		</table>
	</div>
<?php
	// 過濾使用者看過的新聞
	$sql = "select a.`nid`,`title`,`news_t` from `news` as a left join `viewlog` as b on a.`nid` = b.`nid` where a.`nid` in (".implode(',',$ontology).") and b.`nid` is null order by `news_t` desc limit ".$stt->onto_limit;
	$rcmdRes = mysql_query($sql);
?>
	<div data-role="collapsible" data-content-theme="d" data-theme="b">
		<h3>個人化推薦</h3>
		<ul data-role="listview" data-filter="true">
		<?php while($rcmdRow = mysql_fetch_assoc($rcmdRes)) { ?>
			<li><a href="news.php?nid=<?=$rcmdRow['nid']?>"><h3><?=$rcmdRow['title']?></h3><p><?=$rcmdRow['news_t']?></p></a></li>
		<?php } mysql_free_result($rcmdRes);?>
		</ul>
	</div>
</div>
<?php
//==================================================================================================
?>
		</div>
<?php
include_once('footer.php');
?>

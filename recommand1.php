<?php
	include_once("include.php");
	include_once("header.php");
	if(!$ses->uid) tai_location('index.php');
	
	if( isset($_GET['uid']) && is_numeric($_GET['uid']) ) 
		$uid = $_GET['uid'];
	else $uid = $ses->uid;
	$cfg['sevenDay'] = date('Y-m-d',time()-7*24*60*60);
?>
	<div data-role="page" id="-<?php echo $_GET['act']; ?>" data-add-back-btn="true">
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
	<style>
		table.viewTab{ width: 100%; border-collapse: collapse; }
		table.viewTab th, table.viewTab td{ border: 1px solid #000; }
	</style>
	<div data-role="collapsible" data-content-theme="d" data-theme="b">
		<h3>取得目前的登入資訊</h3>
		<p>目前以 <?php echo tai_dbUser('name',NULL,$uid); ?>（UID = <?php echo $uid; ?>）登入。</p>
	</div>
<?php
	// 取得相似度列表
	$simiArry = $simi->get($uid);
	$simiUser = array();
	foreach($simiArry as $k => $v)
		if($simiUser['simi']<$v && $v!=0)
		{
			$simiUser['uid'] = $k;
			$simiUser['simi'] = $v;
		}
?>
	<div data-role="collapsible" data-content-theme="d" data-theme="b">
		<h3>取得相似度列表</h3>
		<table class="viewTab">
			<tr>
				<th>姓名</th>
				<th>相似度</th>
			</tr>
		<?php foreach($simiArry as $k => $v) { ?>
			<tr>
				<th><?=tai_dbUser('name',NULL,$k)?></th>
				<td><?=$v?></td>
			</tr>
		<?php } ?>
		</table>
		<p>相似度最高者：<?=tai_dbUser('name',NULL,$simiUser['uid'])?>（UID = <?=$simiUser['uid']?>）</p>
		<p>註：相似度並非即時更新，若要更新<a href="similarity.php?reload=1" target="similarityUpdate" data-role="button" data-icon="refresh" data-inline="true" data-mini="true" >請按此</a><iframe style="width:0; height:0" id="similarityUpdate" ></iframe></p>
	</div>
<?php
	if(!empty($simiUser))
	{
		// 取得自己讀過的文章與相似者的差異
		$sql = "select a.* from ( select `nid`,`view_t` from `viewlog` where `uid` = {$simiUser['uid']} ) as a left join ( select `nid`,`view_t` from `viewlog` where `uid` = {$uid} ) as b on a.`nid` = b.`nid` where b.`nid` is null order by `view_t` desc limit 100";
		$rcmdRes = mysql_query($sql);
		$rcmd = array();
		while( $rcmdRow = mysql_fetch_assoc($rcmdRes) )
		{
			$sql = "select `title`,`news_t` from `news` where `nid` = '{$rcmdRow['nid']}'";
			$titleRes = mysql_query($sql);
			if( $titleRow = mysql_fetch_assoc($titleRes) )
			{
				$rcmdRow['title'] = $titleRow['title'];
				$rcmdRow['news_t'] = $titleRow['news_t'];
				$rcmd[] = $rcmdRow;
			}
			@mysql_free_result($titleRes);
		}
		@mysql_free_result($rcmdRes);
?>
	<div data-role="collapsible" data-content-theme="d" data-theme="b">
		<h3>最相似者讀過減去你讀過的</h3>
		<table class="viewTab">
			<tr>
				<th>新聞 ID</th>
				<th>閱讀時間</th>
			</tr>
		<?php foreach($rcmd as $v) { ?>
			<tr>
				<th><?=$v['nid']?></th>
				<td><?=$v['view_t']?></td>
			</tr>
		<?php } ?>
		</table>
	</div>
	<div data-role="collapsible" data-content-theme="d" data-theme="b">
		<h3>最相似者讀過，你卻沒看過的新聞</h3>
		<ul data-role="listview" data-filter="true">
		<?php foreach($rcmd as $v) { ?>
			<li><a href="news.php?nid=<?=$v['nid']?>}"><h3><?=$v['title']?></h3><p><?=$v['news_t']?></p></a></li>
		<?php } ?>
		</ul>
	</div>
<?php
	}
	else
	{
?>
	<div data-role="collapsible" data-content-theme="d" data-theme="b">
		<h3>抱歉，你都沒有看過新聞，無法推薦。</h3>
	</div>
<?php
	}
//==================================================================================================
?>
		</div>
<?php
include_once('footer.php');
?>

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
			<h1>相似度推薦展示</h1>
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
	<div data-role="collapsible" data-collapsed="false" data-content-theme="d" data-theme="b">
		<h3>取得目前的登入資訊</h3>
		<p>目前以 <?php echo tai_dbUser('name',NULL,$uid); ?>（UID = <?php echo $uid; ?>）登入。</p>
	</div>
<?php
	// 取得相似度列表
	$simiArry = $simi->get($uid);
	$simiUser = array();
	foreach( array_keys($simiArry) as $i => $k )
	{
		if($i>=3) break;
		if( $simiArry[$k] > 0.00001 )
			$simiUser[] = $k;
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
				<th><a href="?uid=<?=$k?>"><div><?=tai_dbUser('name',NULL,$k)?></div></a></th>
				<td><?=$v?></td>
			</tr>
		<?php } ?>
		</table>
		<?php
		if(isset($simiUser[0])) echo '<p>第①相似者：'.tai_dbUser('name',NULL,$simiUser[0]).'（UID = '.$simiUser[0].'）</p>';
		if(isset($simiUser[1])) echo '<p>第②相似者：'.tai_dbUser('name',NULL,$simiUser[1]).'（UID = '.$simiUser[1].'）</p>';
		if(isset($simiUser[2])) echo '<p>第③相似者：'.tai_dbUser('name',NULL,$simiUser[2]).'（UID = '.$simiUser[2].'）</p>';
		?>
		<p>註：相似度並非即時更新，若要更新<a href="similarity.php" target="similarityUpdate" data-role="button" data-icon="refresh" data-inline="true" data-mini="true" onclick="alert('更新成功後會自動重新整理'); $('#similarityUpdate').one('load',function(){ history.go(0); });" >請按此</a><iframe style="width:0; height:0" id="similarityUpdate" ></iframe></p>
	</div>
<?php
	if(count($simiUser)>0)
	{
		// 取得自己讀過的文章與相似者的差異
		$rand = rand(0,255);
		$sql = "CREATE TEMPORARY TABLE `reco1_$rand` (`nid` int(11) NOT NULL,`view_t` timestamp NOT NULL)";
		tai_mysqlExec($sql);
		foreach( $simiUser as $k => $v )
		{
			$sql = "insert into `reco1_$rand` select a.* from ( select `nid`,`view_t` from `viewlog` where `uid` = {$v} ) as a left join ( select `nid`,`view_t` from `viewlog` where `uid` = {$uid} ) as b on a.`nid` = b.`nid` where b.`nid` is null order by `view_t` desc limit ".($stt->{'simi_'.($k+1).'st'});
			tai_mysqlExec($sql);
		}
		$sql = "select distinct `nid`,`view_t` from `reco1_$rand` order by `view_t`";
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

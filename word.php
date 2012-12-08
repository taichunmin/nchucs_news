<?php
	include_once("include.php");
	include_once("header.php");
	if(!$ses->uid) tai_location('index.php');
	if(isset($_GET['date']) || isset($_GET['rid']))
	{
		if(isset($_GET['date']))
			$ses->word_date = $_GET['date'];
		if(isset($_GET['rid']))
			$ses->word_rid = $_GET['rid'];
		tai_location();
	}
	$rnd = rand(1,100);		// 用來生成暫時 table 用
?>
	<div data-role="page" id="-<?php echo $_GET['act']; ?>" data-add-back-btn="true">
		<div data-role="header" data-position="fixed">
			<h1>關鍵字排行<?=(!empty($ses->word_rid)?" Rid={$ses->word_rid}":'')?></h1>
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
			<div data-role="collapsible-set" data-theme="b" data-content-theme="d">
				<div data-role="collapsible" data-collapsed="true" data-theme="b" data-content-theme="d">
					<h3>篩選日期</h3>
					<ul data-role="listview" data-theme="c" data-dividertheme="d" data-mini="true">
					<?php
						echo '<li'.(empty($ses->word_date)?' data-theme="a"':'').'><a href="?date=" data-ajax="false">全部</a></li>';
						$sql = "select distinct LEFT(`news_t`,10) as 'date' from `news` order by LEFT(`news_t`,10) desc limit 10";
						$dateRes = mysql_query($sql);
						while( $dateRow = mysql_fetch_assoc($dateRes) )
							echo '<li'.(($ses->word_date==$dateRow['date'])?' data-theme="a"':'').'><a href="?date='.$dateRow['date'].'">'.$dateRow['date'].'</a></li>';
						@mysql_free_result($dateRes);
					?>
					</ul>
				</div>
				<div data-role="collapsible" data-collapsed="true" data-theme="b" data-content-theme="d">
					<h3>篩選分類</h3>
					<ul data-role="listview" data-theme="c" data-dividertheme="d" data-mini="true">
					<?php
						echo '<li'.(empty($ses->word_rid)?' data-theme="a"':'').'><a href="?rid=" data-ajax="false">全部</a></li>';
						$sql = "select `rid`,`name` from `rss` order by `rid`";
						$rssRes = mysql_query($sql);
						while( $rssRow = mysql_fetch_assoc($rssRes) )
							echo '<li'.(($ses->word_rid==$rssRow['rid'])?' data-theme="a"':'').'><a href="?rid='.$rssRow['rid'].'">'.$rssRow['name'].'</a></li>';
						@mysql_free_result($rssRes);
					?>
					</ul>
				</div>
			</div>
			<ol data-role="listview" data-inset="true">
			<?php
				$word_filter_sql = '';
				if( !empty($ses->word_date) || !empty($ses->word_rid) )
				{
					$word_filter = array();
					if(!empty($ses->word_date))
						$word_filter[] = " LEFT(`news_t`,10)='{$ses->word_date}' ";
					if(!empty($ses->word_rid))
						$word_filter[] = " `rid` = '{$ses->word_rid}' ";
					$word_filter_sql = "where `nid` in (select `nid` from `news` where ".implode('and',$word_filter).' )';
				}
				$sql = "create temporary table `wordsum$rnd` select `wid` , sum(`cnt`) as 'sum' from `news2word` $word_filter_sql group by `wid` having sum(`cnt`) > 1";
				tai_mysqlExec($sql);
				$sql = "select a.`val` as 'wname', b.`sum` from `wordsum$rnd` as b left join `word` as a on a.`wid` = b.`wid` order by `sum` desc limit 200";
				$wRes = mysql_query($sql);
				while( $wRow = mysql_fetch_assoc($wRes))
					echo '<li>'.$wRow['wname'].' <span class="ui-li-count">'.$wRow['sum'].'</span></li>';
				@mysql_free_result($wRes);
			?>
			</ol>
<?php
//==================================================================================================
?>
		</div>
<?php
include_once('footer.php');
?>

<?php
	include_once("include.php");
	include_once("header.php");
	if(!$ses->uid) tai_location('index.php');
	$title = array(
		'groupByDate' => '依日期看新聞',
		'groupByCategory' => '依分類看新聞',
	);
?>
	<div data-role="page" id="-<?php echo $_GET['act']; ?>" data-add-back-btn="true">
		<div data-role="header" data-position="fixed">
			<h1>今日推薦</h1>
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
	// 新增一個暫時的 TABLE，以便處理新聞
	$rand = rand(0,255);
	$sql = "create temporary table `today_$rand` select `nid` from `news` limit 0;";
		//echo $sql.'<br />';
	tai_mysqlExec($sql);
	
	// 加入同好者推薦
	// 選出前三大同好者
	$sql = "select `uid` from ( select `uid2` as 'uid',`simi` from similarity where `uid1` = '{$ses->uid}' union select `uid1`,`simi` from similarity where `uid2` = '{$ses->uid}' ) as a order by a.simi desc limit 3";
		//echo $sql.'<br />';
	$simiRes = mysql_query($sql);
	for( $i=1; $i<=3 && $simiRow = mysql_fetch_row($simiRes); $i++ )
	{
		// 對每個同好者，取其瀏覽紀錄，存進暫時的 TABLE
		$sql = "insert into `today_$rand` select `nid` from `viewlog` where `uid` = '{$simiRow[0]}' order by `view_t` desc limit ".$stt->{"simi_{$i}st"};
		//echo $sql.'<br />';
		tai_mysqlExec($sql);
	}
	@mysql_free_result($simiRes);
	
	// 加入個人化推薦
	// 取出 ontology TABLE 的推薦結果，存進暫時的 TABLE
	$sql = "insert into `today_$rand` select `nid` from `ontology` where `uid` = '{$ses->uid}' order by `weight` desc limit {$stt->onto_limit}";
		//echo $sql.'<br />';
	tai_mysqlExec($sql);
	
	// 如果要增加新的推薦方法，一樣從這裡繼續加下去
	
	// 開始對暫時 TABLE 中的新聞做處理
	// 先濾除使用者已看過的新聞，再依照時間排序
	$sql = "select c.nid,c.title,c.`news_t` from `today_$rand` as a left join `viewlog` as b on b.uid='{$ses->uid}' and a.nid=b.nid left join `news` as c on a.nid=c.nid where b.nid is null order by `news_t` desc";
		//echo $sql.'<br />';
	$rcmdRes = mysql_query($sql);
	if(mysql_num_rows($rcmdRes)==0)
		tai_location('index.php');
//==================================================================================================
?>
	<ul data-role="listview" data-filter="true">
	<?php while($rcmdRow = mysql_fetch_assoc($rcmdRes)) { ?>
		<li><a href="news.php?nid=<?=$rcmdRow['nid']?>"><h3><?=$rcmdRow['title']?></h3><p><?=$rcmdRow['news_t']?></p></a></li>
	<?php } mysql_free_result($rcmdRes);?>
	</ul>
		</div>
<?php
include_once('footer.php');
?>

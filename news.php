<?php
	include_once("include.php");
	include_once("header.php");
	if(!$ses->uid) tai_location('index.php');
	
	// 讀取新聞
	$_GET['nid'] = intval($_GET['nid']);
	$sql = "select * from `news` where `nid`='{$_GET['nid']}'";
	$newsRes = mysql_query($sql);
	if( $newsRow = mysql_fetch_assoc($newsRes) )
	{
		// 新增閱讀紀錄
		$sql = "select `vid` from `viewlog` where `uid`='{$ses->uid}' and `nid`='{$_GET['nid']}' ";
		$vid = tai_mysqlResult($sql);
		if( isset($vid) )
		{
			$sql = "update `viewlog` set `view_t`=now() where `vid`='$vid'";
			tai_mysqlExec($sql);
		}
		else
		{
			$sql = "insert into `viewlog` (`uid`,`nid`) values ('{$ses->uid}','{$_GET['nid']}')";
			tai_mysqlExec($sql);
		}
		$title = "新聞 ID: {$_GET['nid']}";
	}
	else $title = '此新聞不存在';
	@mysql_free_result($newsRes);
?>
	<div data-role="page" id="news-<?php echo $_GET['nid']; ?>" data-add-back-btn="true">
		<div data-role="header" data-position="fixed">
			<h1><?php echo $title; ?></h1>
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
	if( isset($newsRow) )
	{
		echo "<div style=\"font-size:150%;font-weight: bold\">{$newsRow['title']}</div>";
		echo "<p style=\"text-align: right\">{$newsRow['news_t']}</p>";
		echo "<p>{$newsRow['article']}</p>";
		echo "<a data-role=\"button\" data-theme=\"e\" data-rel=\"external\" href=\"{$newsRow['url']}\" target=\"_blank\">查看原新聞網址</a>";
	}
//==================================================================================================
?>
		</div>
<?php
include_once('footer.php');
?>

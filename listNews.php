<?php
	include_once("include.php");
	include_once("header.php");
	if(!$ses->uid) tai_location('index.php');
	$title = array(
		'groupByDate' => '依日期看新聞',
		'groupByCategory' => '依分類看新聞',
		'fliter' => $_GET['title'],
	);
?>
	<div data-role="page" id="listNews_<?php echo $_GET['act']; ?>" data-add-back-btn="true">
		<div data-role="header" data-position="fixed">
			<h1><?php echo $title[$_GET['act']]; ?></h1>
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
switch($_GET['act'])
{
case 'fliter':
	$get = array();
	foreach(array('rid','date') as $field)
		if( isset( $_GET[$field] ) )
		{
			switch($field)
			{
			case 'rid':
				if( $dck->uint($_GET[$field]) )
					$get[$field] = $_GET[$field];
				break;
			case 'date':
				if( $dck->date($_GET[$field]) )
					$get[$field] = $_GET[$field];
				break;
			}
		}
	if(count($get)==0) tai_location('index.php');
	echo '<ul data-role="listview" data-filter="true">';
	// 組合 SQL
	$sql_where = array();
	if(isset($get['date'])) $sql_where[] .= " LEFT(`news_t`,10)='{$get['date']}' ";
	if(isset($get['rid'])) $sql_where[] .= " `rid`='{$get['rid']}' ";
	$sql = "select `nid`,`title`,`news_t` from `news` where ".implode('and',$sql_where)." order by `news_t` desc";
	$newsRes = mysql_query($sql);
	while( $newsRow = mysql_fetch_assoc($newsRes) )
	{
		echo "<li><a href=\"news.php?nid={$newsRow['nid']}\"><h3>{$newsRow['title']}</h3><p>{$newsRow['news_t']}</p></a></li>";
	}
	@mysql_free_result($newsRes);
	echo '</ul>';
	break;
case 'groupByCategory':
	echo '<ul data-role="listview">';
	$sql = "select * from `newsbycategory` order by `cnt` desc";
	$dateRes = mysql_query($sql);
	while( $dataRow = mysql_fetch_assoc($dateRes) )
	{
		echo "<li><a href=\"listNews.php?act=fliter&rid={$dataRow['rid']}&title=".urlencode(str_replace('UDN','',$dataRow['name']))."\">".str_replace('UDN','',$dataRow['name'])."<span class=\"ui-li-count\">{$dataRow['cnt']}</span></a></li>";
	}
	@mysql_free_result($dateRes);
	echo '</ul>';
	break;
case 'groupByDate':
default:
	echo '<ul data-role="listview">';
	$sql = "select * from `newsbydate` order by `date` desc";
	$dateRes = mysql_query($sql);
	while( $dataRow = mysql_fetch_assoc($dateRes) )
	{
		echo "<li><a href=\"listNews.php?act=fliter&date={$dataRow['date']}&title={$dataRow['date']}\">{$dataRow['date']}<span class=\"ui-li-count\">{$dataRow['cnt']}</span></a></li>";
	}
	@mysql_free_result($dateRes);
	echo '</ul>';
	break;
}
//==================================================================================================
?>
		</div>
<?php
include_once('footer.php');
?>

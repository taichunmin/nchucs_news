<?php
	require_once('include.php');
	
	$sql = "select `nid` from `news` group by `url` having count(*) > 1 ";
	$res = mysql_query($sql);
	$nids = '';
	while( $row = mysql_fetch_assoc($res) )
	{
		$nids[]=$row['nid'];
	}
	@mysql_free_result($res);
	$sql = "delete from `news` where `nid` in ('".@implode("','",$nids)."') ";
	tai_mysqlExec($sql);
	echo "刪除了 ".mysql_affected_rows()." 筆資料";
?>
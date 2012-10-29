<?php
/*
	目前功能：
		1.清除 word 內的全形空白字元
		2.找出重複的 word, 保留 wid 最小的 word, 並把 cnt 加總後，刪除多餘的 word。
*/
	require_once('include.php');
	
	// 清除全形空白
	$sql = "update `word` set `val` = replace(`val`,'　','')";
	tai_mysqlExec($sql);
	$cli->p("Clear Space : affect %d rows\n",mysql_affected_rows());
	
	// 找出重複的 word
	$sql = "create temporary table `word_unique` select min(`wid`) as 'wid',`val` from `word` group by `val` having count(`val`)>1";
	tai_mysqlExec($sql);
	
	// 刪除 word
	$sql = "select `word`.`wid` as 'widfrom',`word_unique`.`wid` as 'widto' from `word`,`word_unique` where `word`.`val`=`word_unique`.`val` and `word`.`wid` != `word_unique`.`wid`";
	$dupRes = mysql_query($sql);
	$dupCnt = mysql_num_rows($dupRes);
	$pc = new percent_C($dupCnt);
	$pc->p("move word count... %d%% of ".$dupCnt);
	$dupWid = array();
	while( $dupRow = mysql_fetch_assoc($dupRes))
	{
		if(!is_numeric($dupRow['widfrom']) || !is_numeric($dupRow['widto'])) continue;
		$sql = "update `news2word` as a,`news2word` as b set a.`cnt`=(a.`cnt`+b.`cnt`) where a.`wid`='{$dupRow['widto']}' and b.`wid`='{$dupRow['widfrom']}' and a.`nid`=b.`nid ";
		// update `news2word` as a,`news2word` as b set a.`cnt`=(a.`cnt`+b.`cnt`) where a.`wid`='1977' and b.`wid`='39972' and a.`nid`=b.`nid`
		//$cli->p($sql."\n");
		tai_mysqlExec($sql);
		$dupWid[] = $dupRow['widfrom'];
		//$cli->pause();
		$pc->c();
	}
	$cli->p("\nNext step will delete duplicate word, ");
	$cli->pause();
	$sql = "delete from `news2word` where `wid` in (".implode(',',$dupWid).")";
	//$cli->p($sql."\n");
	tai_mysqlExec($sql);
	$sql = "delete from `word` where `wid` in (".implode(',',$dupWid).")";
	//$cli->p($sql."\n");
	tai_mysqlExec($sql);
	@mysql_free_result($dupRes);
	
?>
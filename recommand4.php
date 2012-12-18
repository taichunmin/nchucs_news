<?php
include_once('include.php');
/*
data=>array(uid=>array('count_nid'=>array(cid=>n),'tf_list'=>array(cid=>array(term=>freq))))
data=>array(
	uid=>array(
		'count_nid'=>array(cid=>n),
		'tf_list'=>array(
			cid=>array(term=>freq)

			),
		),
	);
*/
function rc4getData()
{
	$d = array();
	$sql = "select * from `user`";
	$uidRes = mysql_query($sql);
	while( $uidRow = mysql_fetch_assoc($uidRes) )
	{
		$uidRow['uid'] = intval($uidRow['uid']);
		$sql = "select `rid`,count(`nid`) as 'count_nid' from `viewlog_rid` where `uid`='{$uidRow['uid']}' group by `rid`";
		$ridRes = mysql_query($sql);
		while( $ridRow = mysql_fetch_assoc($ridRes))
		{
			$ridRow['rid'] = intval($ridRow['rid']);
			$d[ $uidRow['uid'] ][ 'count_nid' ][ $ridRow['rid'] ] = intval($ridRow['count_nid']);
			$sql = "select `nid` from `viewlog_rid` where `uid`='{$uidRow['uid']}' and `rid`='{$ridRow['rid']}'";
			$nidRes = mysql_query($sql);
			$nid = array();
			while( $nidRow = mysql_fetch_assoc($nidRes) )
				$nid[] = $nidRow['nid'];
			@mysql_free_result($nidRes);
			$nidcomma = implode(',',$nid);
			$sql = "select `wid`,sum(`cnt`) as 'sum' from `news2word` where `nid` in ($nidcomma) group by `wid` ";
			$wordRes = mysql_query($sql);
			while( $wordRow = mysql_fetch_assoc($wordRes))
				$d[ $uidRow['uid'] ][ 'tf_list' ][ $ridRow['rid'] ][ intval($wordRow['wid']) ] = intval($wordRow['sum']);
			@mysql_free_result($wordRes);
		}
		@mysql_free_result($ridRes);
	}
	@mysql_free_result($uidRes);
	return $d;
}
file_put_contents('recommand4_out.txt',var_export(rc4getData(),true));
?>
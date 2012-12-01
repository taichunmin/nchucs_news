<?php
	include("include.php");
	
	function getCateTerm($uid)
	{
		$sql = "select * from `viewlog` where `uid` = '$uid'";
		$viewRes = mysql_query($sql);
		$view = array();
		while( $viewRow = mysql_fetch_assoc($viewRes) )
			$view[] = $viewRow['nid'];
		$view = implode(',',$view);
		$sql = "select `nid`,`rid` from `news` where `nid` in ($view)";
		$cvRes = mysql_query($sql);
		$cv = array();
		while( $cvRow = mysql_fetch_assoc($cvRes) )
			$cv[$cvRow['rid']][] = $cvRow['nid'];
		$res = array();
		foreach( $cv as $cate => $cnids )
		{
			$cnids = implode(',',$cnids);
			$sql = "select `wid`,sum(`cnt`) as 'cnt' from `news2word` where `nid` in ($cnids) group by `wid`";
			$termRes = mysql_query($sql);
			while( $termRow = mysql_fetch_assoc($termRes) )
				$res[$cate][ $termRow['wid'] ] = $termRow['cnt'];
		}
		ksort($res);
		return $res;
	}
	
	echo '<pre>'.var_export(getCateTerm(1),true).'</pre>';
?>
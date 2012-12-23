<?php
	include_once('include.php');
	// uid => array( count_nid => cid => n , tf_list => cid => term => freq )
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
	// cid => nid => tid => cnt
	function getNewNewsCateTerm()
	{
		global $lastDate;
		
		// 取得最後的日期
		$sql = "select `date` from `newsbydate` order by `date` desc limit 1";
		$lastDate = tai_mysqlResult($sql);
		// echo $lastDate;
		$sql = "select `rid`,`nid` from `news` where LEFT(`news_t`,10) = '$lastDate'";
		$new = array();
		$newRes = mysql_query($sql);
		while( $newRow = mysql_fetch_assoc($newRes) )
		{
			$newRow['rid'] = intval($newRow['rid']);
			$newRow['nid'] = intval($newRow['nid']);
			$sql = "select `wid`,`cnt` from `news2word` where `nid` = '{$newRow['nid']}' order by `cnt` desc ";
			$wordRes = mysql_query($sql);
			while( $wordRow = mysql_fetch_assoc($wordRes) )
				$new[ $newRow['rid'] ][ $newRow['nid'] ][ intval($wordRow['wid']) ] = intval($wordRow['cnt']);
			@mysql_free_result($wordRes);
		}
		@mysql_free_result($newRes);
		return $new;
	}
	$data = rc4getData();
	$new = getNewNewsCateTerm();
	
	//tai_vardebug($data);
	//資料preset
	$classSelect=41;
	$termSelect=0;
	/*演算法開始*/
	foreach($data as $uid => $cid_list){
		/*Step1：新建一個使用者CLASS的權重表=>加總選定的CLASS的新聞總數=>標準化*/
		arsort($cid_list['count_nid']);//如果資料抓出來就有遞減排序，可刪
		if($classSelect>count($cid_list)){
			$cid_weight_list=$cid_list['count_nid'];
		}
		else{
			$cid_weight_list=array_slice($cid_list['count_nid'],0,$classSelect,true);
		}
		//tai_vardebug($cid_weight_list);//測試輸出使用者排名前$classSelect的類別
		$sum_nid=array_sum($cid_weight_list);
		$cid_tid_weight=array();
		foreach($cid_weight_list as $cid => $weight){
			$cid_weight_list[$cid]/=$sum_nid;
			/*Step2：新建term的權重表=>加總CLASS下的freq總數=>標準化*/
			arsort($cid_list['tf_list'][$cid]);//如果資料抓出來就有遞減排序，可刪
			if($termSelect>count($cid_list['tf_list'][$cid])){
				$cid_tid_weight[$cid]=$cid_list['tf_list'][$cid];
			}
			else{
				$cid_tid_weight[$cid]=array_slice($cid_list['tf_list'][$cid],0,$termSelect,true);
			}
			tai_vardebug($cid_tid_weight[$cid]);//測試輸出使用者類別下排名前$termSelect的詞
			$sum_freq=array_sum($cid_tid_weight[$cid]);
			foreach($cid_tid_weight[$cid] as $tid => $freq){
				$cid_tid_weight[$cid][$tid]/=$sum_freq;//兩項表不先相乘，等HIT在乘，盡可能減少運算空間
			}//跳出=>該類的詞權重表完成
			/*echo 'uid='.$uid.'cid='.$cid.'的詞權重表：'; 
			tai_vardebug($cid_tid_weight[$cid]);
			echo '<br />'; /**/
			/*Step：比較新進新聞*/
			if(is_array($new[$cid]))
				foreach($new[$cid] as $nid =>$tf_list){
					if($intersect=array_intersect_key($tf_list,$cid_tid_weight[$cid])){
						/*tai_vardebug($intersect);
						echo '<br />'; /**/
						foreach($intersect as $tid =>$freq){
							$result[$uid][$nid]+=$freq*$cid_tid_weight[$cid][$tid]*$cid_weight_list[$cid];
						}
					}
			}//一個類別的新進新聞被比對完
		}//跳出=>該使用者類別的權重表完成
		/*echo 'uid='.$uid.'的類別權重表：'; 
		tai_vardebug($cid_weight_list);
		echo '<br />'; /**/
		//arsort($result[$uid]);  //不可刪
		/*echo 'uid='.$uid.'的新進新聞權重表：';
		echo '<br />'; 
		tai_vardebug($result[$uid]);
		echo '<br />'; /**/
	}
	//tai_vardebug($result);//result=array(uid=>array(nid=>weight))
	$sql = "delete from `ontology` where `date` = '$lastDate'";
	$sql = "insert into `ontology` (`uid`,`date`,`nid`,`weight`) values ";
	$tmp = array();
	foreach( array_keys($result) as $uid )
		foreach( array_keys($result[$uid]) as $nid )
			$tmp[] = " ($uid,'$lastDate',$nid,{$result[$uid][$nid]}) ";
	$sql .= implode(',',$tmp);
	//tai_vardebug($tmp);//result=array(uid=>array(nid=>weight))
	tai_mysqlExec($sql);
?>
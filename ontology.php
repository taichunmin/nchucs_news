<?php
	include_once('include.php');//資料庫連接
	
	// uid => array( count_nid => cid => n , tf_list => cid => term => freq )
	//使用者資料:(看過新聞數=>分種類=>分類底下的新聞數，
	//            看過新聞的關鍵字=>類別=>關建字=>出現次數)  
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
				if(count($nid)==0) continue;
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
	//類別下新聞所用關鍵字的出現次數
	function getNewNewsCateTerm($date = null)
	{
		
		if( !empty($date) )
		{
			$sql = "select count(*) from `newsbydate` where `date`='$date'";
			$dateExist = tai_mysqlResult($sql);
			if($dateExist==0) unset($date);
		}
		if( empty($date) )
		{
			// 取得最後的日期
			$sql = "select `date` from `newsbydate` order by `date` desc limit 2";
			$dateRes = mysql_query($sql);
			while( $dateRow = mysql_fetch_assoc($dateRes) )
				$date[] = $dateRow['date'];
			@mysql_free_result($dateRes);
		}
		$lastDate = implode("','",$date);
		// echo $lastDate;
		$sql = "select `rid`,`nid` from `news` where LEFT(`news_t`,10) in ('$lastDate') ";
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
	
	function ontology($date = null)//比對瀏覽歷史跟個人本體興趣，找出適合推薦的新聞
	{
		$data = rc4getData();//使用者資料
		$new = getNewNewsCateTerm($date);//新的新聞的分類跟關鍵字
		
		//tai_vardebug($new);
		//資料preset
		$classSelect=41;//新聞分類預設要取全部41種類分類(可以讓使用者自己選擇要取前N樣)
		$termSelect=0;//新聞的關鍵字(我們自己斷詞出來的)
		$result = array();//推薦結果
		/*演算法開始*/
		foreach($data as $uid => $cid_list){//使用者擁有的分類
			/*Step1：新建一個使用者CLASS的權重表=>加總選定的CLASS的新聞總數排序=>標準化*/
			arsort($cid_list['count_nid']);//如果資料抓出來就有遞減排序，(如果資料進來已經是排好序的，這行就可刪)
			if($classSelect>count($cid_list)){//如果使用者自己設定的分類總數>自己看過的分類種數，那只給使用者分類總數
				$cid_weight_list=$cid_list['count_nid'];
			}
			else{//
				$cid_weight_list=array_slice($cid_list['count_nid'],0,$classSelect,true);
			}
			//tai_vardebug($cid_weight_list);//測試輸出使用者排名前$classSelect的類別
			$sum_nid=array_sum($cid_weight_list);//選定新聞類別的新聞總數全部新聞的數目
			$cid_tid_weight=array();//用來存放選定類別底下新聞所含的關鍵字出現的次數
			foreach($cid_weight_list as $cid => $weight){//選定類別下有幾篇新聞
				$cid_weight_list[$cid]/=$sum_nid;//算出各個類別所佔的百分比
				/*Step2：新建term的權重表=>加總CLASS下的freq總數=>標準化*/
				arsort($cid_list['tf_list'][$cid]);//如果資料抓出來就有遞減排序，可刪
				//tai_vardebug($cid_list['tf_list'][$cid]);
				if($termSelect<=0 || $termSelect>count($cid_list['tf_list'][$cid])){//如果使用者設定的關鍵字總數比自己看過得還多
					$cid_tid_weight[$cid]=$cid_list['tf_list'][$cid];
				}
				else{
					$cid_tid_weight[$cid]=array_slice($cid_list['tf_list'][$cid],0,$termSelect,true);
				}
				//tai_vardebug($cid_tid_weight[$cid]);//測試輸出使用者類別下排名前$termSelect的詞
				$sum_freq=array_sum($cid_tid_weight[$cid]);//所有關鍵字出現次數加起來
				foreach($cid_tid_weight[$cid] as $tid => $freq){
					$cid_tid_weight[$cid][$tid]/=$sum_freq;//兩項表不先相乘，等HIT在乘，盡可能減少運算空間
				}//跳出=>該類的詞權重表完成
				/*echo 'uid='.$uid.'cid='.$cid.'的詞權重表：'; 
				tai_vardebug($cid_tid_weight[$cid]);
				echo '<br />'; /**/
				/*Step：比較新進新聞*/
				if(is_array($new[$cid]))//取得新進新聞的類別
					foreach($new[$cid] as $nid =>$tf_list){//類別下的其中一篇新進新聞跟每個新聞所用的關鍵字的頻率
						if($intersect=array_intersect_key($tf_list,$cid_tid_weight[$cid])){//如果新進新聞的關鍵字也有出現在使用者選擇的關鍵字才往下走
							/*tai_vardebug($intersect);                                    //這個新進新聞關鍵字跟他的數量和使用者在這個類別下看過新聞的關鍵字頻率加總
							                                                               //                               (只在乎類別下使用者看過的關鍵字，不在乎是來自甚麼新聞)
							echo '<br />'; /**/
							foreach($intersect as $tid =>$freq){//最後推薦評分加總(交集中出現的關鍵字(新進新聞中的關鍵字的頻率)*關鍵字在使用者類別之下出現的比例*類別對於使用者的比例)
								$result[$uid][$nid]+=$freq*$cid_tid_weight[$cid][$tid]*$cid_weight_list[$cid];
							}
						}
				}//一個類別的新進新聞被比對完
			}//跳出=>該使用者類別的權重表完成
			/*echo 'uid='.$uid.'的類別權重表：'; 
			tai_vardebug($cid_weight_list);
			echo '<br />'; /**/
			arsort($result[$uid]);  //不可刪
			/*echo 'uid='.$uid.'的新進新聞權重表：';
			echo '<br />'; 
			tai_vardebug($result[$uid]);
			echo '<br />'; /**/
		}
		//tai_vardebug($result);//result=array(uid=>array(nid=>weight))
		return $result;
	}
	$result = ontology(null);
	$sql = "TRUNCATE `ontology`";
	tai_mysqlExec($sql);
	for( $i=0; $i<count($result); $i+=10 )
	{
		$sliceResult = array_slice($result, $i, 10, true);
		$sql = "insert into `ontology` (`uid`,`nid`,`weight`) values ";
		$tmp = array();
		foreach( array_keys($sliceResult) as $uid )
		{
			$i = 0;
			foreach( array_keys($sliceResult[$uid]) as $nid )
			{
				if($i++>=100) break;
				$tmp[] = " ($uid,$nid,{$sliceResult[$uid][$nid]}) ";
			}
		}
		$sql .= implode(',',$tmp);
		//tai_vardebug($tmp);//sliceResult=array(uid=>array(nid=>weight))
		tai_mysqlExec($sql);
	}
?>
<?php
	include('include.php');
	/*
		term: select `wid`,sum(`cnt`) from `news2word` where `nid` in (2902,2904,2954,3493) group by `wid`
		
		array_keys();
		count();
		sort();  rsort();  ksort();  krsort();
	*/
	/*
	data->uid
		  cid->term
			   freq
	*/
	$data = array();
	$sql = "select `uid` from `user`";
	$uidRes = mysql_query($sql);
	while( $uidRow = mysql_fetch_assoc($uidRes) )
	{
		$data[$uidRow['uid']] = getCateTerm($uidRow['uid']);
		if( count($data[$uidRow['uid']])==0 ) unset($data[$uidRow['uid']]);
	}
	@mysql_free_result($uidRes);
	
	$user_termFreq=array();//存根號平方合 Cosine similarity分母(兩個使用者)
	$userList=array();//為了要以index定位，看能不能省去
	foreach($data as $uid => $arr_cid){//使用者看過新聞的所有類別
		$merge_termFreq=array();//將class的term&frequency合併
		$user=$arr_cid;
		$userList[]=$uid;//記錄算了幾個USER
		foreach($user as $cid => $arr_termFreq){//使用者類別下關鍵字及其頻率
			$termList=$arr_termFreq;//記錄使用者用過的關鍵字和頻率
			foreach($termList as $term =>$frequency){//針對每個關鍵字和頻率計算
			    //
				$merge_termFreq=array_pad($merge_termFreq,count($merge_termFreq)+$frequency,$term);//重覆append
			}
		}
		$merge_termFreq=array_count_values($merge_termFreq);//計算次數 關鍵字和其出現次數
		foreach($merge_termFreq as $term => $frequency){//平方相加(一個使用者)
			$temp+=$frequency*$frequency;
		}
		$temp=sqrt($temp);//開根號
		$user_termFreq[$uid] =$temp;//算完這個USER的值，存進去
		//echo $temp.'<br />';
		$temp=0;
	}
	foreach($userList as $index => $uid){//算所有使用者間的相似度
		$arr_cid=$data[$uid];//第一人
		while($arr_cid2=$data[$userList[++$index]]){//第二人
			//Step:兩人對class的關係表
			//print_r(array_keys($arr_cid));
			//print_r(array_keys($arr_cid2));
			//echo '<br />';
			$merge_cid=array_merge(array_keys($arr_cid),array_keys($arr_cid2));//
			sort($merge_cid);
			$merge_cid=array_count_values($merge_cid);
			foreach($merge_cid as $cid => $case){
				if($case==2){//CLASS中兩人對term的關係表
					$merge_tid=array_merge(array_keys($arr_cid[$cid]),array_keys($arr_cid2[$cid]));//array_keys():取ARRAY的INDEX值;array_merge():UNION 兩個ARRAY
					                                                                               
					sort($merge_tid);//排序兩個使用者用過的關鍵字
					$merge_tid=array_count_values($merge_tid);//統計每個關鍵字出現的次數，由此知道兩個使用者交集的關鍵字($tid =>1 OR 2)
					foreach($merge_tid as $tid => $case){//如果"$tid =>2，表示該關鍵字為兩人皆使用
						if($case==2){
							$similarity+=$arr_cid[$cid][$tid]*$arr_cid2[$cid][$tid];//Cosine similarity分子(兩個使用者相同類別下相同關建字)
						}
					}
				}
			}
			if($user_termFreq[$uid]*$user_termFreq[$userList[$index]]!=0){//分母中第一個人和第二個人相乘不是等於0，才往下算
				$similarity/=$user_termFreq[$uid]*$user_termFreq[$userList[$index]];
			}
			//echo 'uid='.$uid.'跟uid='.$userList[$index].'的同好度為:'.$similarity.'<br />';
			$simi->set($uid, $userList[$index], $similarity);//A對B相似度多少計算完後存入SET中
			$similarity=0;
		}
		
	}
	function getCateTerm($uid)
	{
		// 取得瀏覽紀錄
		$sql = "select * from `viewlog` where `uid` = '$uid'";
		$viewRes = mysql_query($sql);
		$view = array();
		while( $viewRow = mysql_fetch_assoc($viewRes) )
			$view[] = $viewRow['nid'];
		@mysql_free_result($viewRes);
		$view = implode(',',$view);
		// 沒有瀏覽紀錄，直接回傳空陣列
		if($view=='') return array();
		
		// 找出新聞所屬的分類
		$sql = "select `nid`,`rid` from `news` where `nid` in ($view) order by `rid`";
		$cvRes = mysql_query($sql);
		$cv = array();
		while( $cvRow = mysql_fetch_assoc($cvRes) )
			$cv[$cvRow['rid']][] = $cvRow['nid'];
		@mysql_free_result($cvRes);
		
		// 利用分類下的新聞，取得所有的關鍵字
		$res = array();
		foreach( $cv as $cate => $cnids )
		{
			$cnids = implode(',',$cnids);
			$sql = "select `wid`,sum(`cnt`) as 'cnt' from `news2word` where `nid` in ($cnids) group by `wid`";
			$termRes = mysql_query($sql);
			while( $termRow = mysql_fetch_assoc($termRes) )
				$res[intval($cate)][ intval($termRow['wid']) ] = intval($termRow['cnt']);
			@mysql_free_result($termRes);
		}
		return $res;
	}
?>
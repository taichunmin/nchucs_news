<?php
	include('include.php');
	/*
		term: select `wid`,sum(`cnt`) from `news2word` where `nid` in (2902,2904,2954,3493) group by `wid`
		
		array_keys();
		count();
		sort();  rsort();  ksort();  krsort();
	*//*
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
	$user_termFreq=array();//存更號平方合
	$userList=array();//為了要以index定位，看能不能省去
	foreach($data as $uid => $arr_cid){
		$merge_termFreq=array();//將class的term&frequency合併
		$user=$arr_cid;
		$userList[]=$uid;
		foreach($user as $cid => $arr_termFreq){
			$termList=$arr_termFreq;
			foreach($termList as $term =>$frequency){
				$merge_termFreq=array_pad($merge_termFreq,count($merge_termFreq)+$frequency,$term);//重覆append
			}
		}
		$merge_termFreq=array_count_values($merge_termFreq);//計算次數
		foreach($merge_termFreq as $term => $frequency){
			$temp+=$frequency*$frequency;
		}
		$temp=sqrt($temp);
		$user_termFreq[$uid] =$temp;
		echo $temp;
		echo '<br />';
		$temp=0;
	}
	foreach($userList as $index => $uid){
		$arr_cid=$data[$uid];//第一人
		while($arr_cid2=$data[$userList[++$index]]){//第二人
			//Step:兩人對class的關係表
			print_r(array_keys($arr_cid));
			print_r(array_keys($arr_cid2));
			echo '<br />';
			$merge_cid=array_merge(array_keys($arr_cid),array_keys($arr_cid2));
			sort($merge_cid);
			$merge_cid=array_count_values($merge_cid);
			foreach($merge_cid as $cid => $case){
				if($case==2){//CLASS中兩人對term的關係表
					$merge_tid=array_merge(array_keys($arr_cid[$cid]),array_keys($arr_cid2[$cid]));
					sort($merge_tid);
					$merge_tid=array_count_values($merge_tid);
					foreach($merge_tid as $tid => $case){
						if($case==2){
							$similarity+=$arr_cid[$cid][$tid]*$arr_cid2[$cid][$tid];
						}
					}
				}
			}
			if($user_termFreq[$uid]*$user_termFreq[$userList[$index]]!=0){
				$similarity/=$user_termFreq[$uid]*$user_termFreq[$userList[$index]];
			}
			echo 'uid='.$uid.'跟uid='.$userList[$index].'的相似度為:';
			echo $similarity;
			echo '<br />';
			$similarity=0;
		}
		
	}
	function getCateTerm($uid)
	{
		$sql = "select * from `viewlog` where `uid` = '$uid'";
		$viewRes = mysql_query($sql);
		$view = array();
		while( $viewRow = mysql_fetch_assoc($viewRes) )
			$view[] = $viewRow['nid'];
		@mysql_free_result($viewRes);
		$view = implode(',',$view);
		if($view=='') return array();
		$sql = "select `nid`,`rid` from `news` where `nid` in ($view)";
		$cvRes = mysql_query($sql);
		$cv = array();
		while( $cvRow = mysql_fetch_assoc($cvRes) )
			$cv[$cvRow['rid']][] = $cvRow['nid'];
		@mysql_free_result($cvRes);
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
		ksort($res);
		return $res;
	}
?>
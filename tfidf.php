<?php
	include_once('include.php');
	set_time_limit(0);		// 執行時間無限
	function tid_cidFreq(){
		$sql = "SELECT `wid`,`rid`,`sum` FROM `wordridsum`";
		$res = mysql_query($sql);
		$data = array();
		while( $row = mysql_fetch_assoc($res) )
			$data[ intval($row['wid']) ][ intval($row['rid']) ] = intval($row['sum']);
		@mysql_free_result($res);
		return $data;
	}
	function get_cidFreq(){
		$sql = "select `rid`,sum(`sum`) as 'sum' from `ridnewswordsum` group by `rid`";
		$res = mysql_query($sql);
		$data=array();
		while( $row = mysql_fetch_assoc($res) )
			$data[ intval($row['rid']) ] = intval($row['sum']);
		return $data;
	}
	function TFIDF()
	{
		$cid_allFreq=get_cidFreq();
		$new=tid_cidFreq();
		$count=count($new);
		$result=array();
		//print_r($new);
		$log_list = array();
		for( $i=1;$i<=$count;$i++)
			$log_list[$i] = log($count/floatval($i));
		foreach($new as $tid => $cid_list){
			$idf=$log_list[count($cid_list)];
			foreach($cid_list as $cid => $freq){
				$tf=$new[$tid][$cid]/$cid_allFreq[$cid];
				$tfidf=$tf*$idf;
				$result[$cid][$tid]=$tfidf;
			}
			arsort($result[$cid]);
		}
		/*foreach($result as $cid => $tid_list){
			foreach($tid_list as $tid => $freq){
				echo $cid.'=>';
				echo $tid.'=>';
				echo $result[$cid][$tid];
				echo '<br /r>';
				break;
			}
		}*/
		//print_r($result);
		return $result;
	}
	TFIDF();
?>
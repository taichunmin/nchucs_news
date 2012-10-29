<?php
	include_once("include.php");
	// 常用的函式庫
	
	set_time_limit(0);
	// 設定程式執行時間無限
	
	$cfg = array(
		'today' => date('Y-m-d'),
		'recommand_id' => 1,
		'sevenDays' => date('Y-m-d',time()-7*24*60*60),
	);
	
	// 取得尚未推薦的使用者列表
	$sql = "select `uid` from `user` where `uid` not in ( select `uid` from `recommend` where `date` = '{$cfg['today']}' and `method` = {$cfg['recommand_id']} )";
	$uidRes = mysql_query($sql);
	while( $uidRow = mysql_fetch_assoc($uidRes) )
	{
		echo "uidRow['uid'] = {$uidRow['uid']}";
		
		$sql = "select `nid` from `viewlog` where `uid` = '{$uidRow['uid']}' and `view_t`>'{$cfg['sevenDays']}'";
		$viewRes = mysql_query($sql);
		$viewCnt = mysql_num_rows($viewRes);	// 7 天內閱讀的新聞數量
		$view = array();
		while($viewRow = mysql_fetch_assoc($viewRes))
			$view[] = $viewRow['nid'];		// 瀏覽紀錄
		@mysql_free_result($viewRes);
		$viewNidStr = implode(',',$view);
		/* debug view
			echo "<pre>".var_export($view,true)."</pre>";
			echo $viewNidStr;
			exit(0);
		//*/
		
		$sql = "select `rid`,count(`rid`) as 'cnt' from `news` where `nid` in ( $viewNidStr )";
		$cateRes = mysql_query($sql);
		$cate = array();	// 設定初始值
		while( $cateRow = mysql_fetch_assoc($cateRes) )
			$cate[] = $cateRow;		// 7 天內各分類的新聞閱讀數量
		/* debug display
			echo "<pre>".var_export($cate,true)."</pre>";
			exit(0);
		//*/
		
		foreach( $cate as $category )
		{
			$sql = "select `nid` from `viewlog` where `uid` = '{$uidRow['uid']}' where `view_t`<'{$cfg['sevenDays']}'";
		}
		
	}
	@mysql_free_result($uidRes);
?>
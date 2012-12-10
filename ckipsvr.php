<?php

	require_once('include.php');
	//set_time_limit(0);  // 這個程式的執行時間限制
	
	// Get the IP address for the target host. 
	$ipv4 = '140.109.19.104';
	
	// Create a TCP/IP socket. 
	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if ($socket === false)
		die("socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n");
	
	$result = socket_connect($socket, $ipv4, 1501);
	// 斷詞服務採用TCP Socket連線傳輸資料，伺服器IP位址為 140.109.19.104 ，連接埠為 1501
	if ($result === false)
		echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
	
	$in = "\n";
	socket_write($socket, $in, strlen($in));
	
	$sql = "select * from `news` where `ckipsvr`=0";
	$res = mysql_query($sql);
	$numRows = mysql_num_rows($res);
	$pc = new percent_C($numRows);
	$pc->p('ckipsvr...%3d%% of '.$numRows);
	while( $news = mysql_fetch_assoc($res) )
	{
		$news['article'] = htmlspecialchars_decode($news['title'].'。'.$news['article']);
		$news['article'] = str_replace(array("\r","\n","<",">","&","'","/",'\\'),'',$news['article']);
		$news['article'] = str_replace(array('「','『','』','」'),'。',$news['article']);
		$news['article'] = preg_replace('/[A-Za-z]/u','',$news['article']);
		$in = <<<XML
<?xml version="1.0" ?>
<wordsegmentation version="0.1">
<option showcategory="1" />
<authentication username="taichunmin" password="12324123" />
<text>{$news['article']}</text>
</wordsegmentation>
XML;
		$in = iconv('utf-8','big5//IGNORE',$in);
		//echo PHP_EOL.'$in = '.$in.PHP_EOL;
		socket_write($socket, $in, strlen($in));
		$ckipres = '';
		while ($out = socket_read($socket, 2048, PHP_NORMAL_READ))
		{
			$ckipres .= iconv('big5','utf-8//IGNORE',$out);
			if(strpos($ckipres,'</wordsegmentation>')!==false)
				break;
		}
		//echo $ckipres;
		
		// 紀錄斷詞結果 0: 尚未斷詞， 1: 斷詞成功， 2: 斷詞失敗
		if( strpos($ckipres,'<processstatus code="0">') !== false )
			$ckipstatus = 1;
		else $ckipstatus = 2;
		$sql = "update `news` set `ckipsvr` = $ckipstatus where `nid`={$news['nid']}";
		tai_mysqlExec($sql);
		$pc->c();
		if($ckipstatus==2)continue;
		
		// 整理段詞結果
		$pos = array( strpos($ckipres,'<result>')+8 ,strpos($ckipres,'</result>'));
		$ckipres = substr($ckipres, $pos[0], $pos[1]-$pos[0]);
		$ckipres = str_replace(array('<sentence>','</sentence>',"\r","\n"),'',$ckipres);
		$ckipres = str_replace('@','　',$ckipres);
		$ckipres = preg_replace('/　[^(]*\([^)]*CATEGORY\)/u','',$ckipres);
		
		// 將詞切成陣列
		preg_match_all('/(?<=　)([^　]*?)\((\w+)\)(?=　)/u',$ckipres,$ckip);
		unset($ckipres);
		array_shift($ckip);
		foreach($ckip[1] as $k => $v)
			if($v[0]!='N') unset($ckip[0][$k]);
		$ckip=$ckip[0];
		
		// 計算頻率
		unset($ckipcnt);
		foreach($ckip as &$word)
		{
			$word = @mysql_escape_string($word);
			$ckipcnt[$word]=$ckipcnt[$word]+1;
		}
		unset($ckip);
		arsort($ckipcnt);
		
		unset($ckipins);
		foreach($ckipcnt as $k => $v)
			$ckipins[]="('$k','$v')";
		$rand = rand(1,16); 
		// 先將資料存進一個暫時的 TABLE 
		$sql = "CREATE temporary TABLE `word_tmp$rand` (`value` VARCHAR(32) NOT NULL COLLATE 'utf8_unicode_ci',`count` INT(11) NOT NULL)";
		tai_mysqlExec($sql);
		$sql = "insert into `word_tmp$rand`(`value`,`count`) values ".implode(",",$ckipins)."";
		tai_mysqlExec($sql);
		// 新增沒有出現過的 word
		$sql = "insert into `word` (`val`) select `value` from `word_tmp$rand` where not exists (select * from `word` where `val`=`value`)";
		tai_mysqlExec($sql);
		// 紀錄新聞中出現過的詞
		$sql = "insert into `news2word` (`nid`,`wid`,`cnt`) select '{$news['nid']}',(select `wid` from `word` where `val`=`value` limit 1 ),`count` from `word_tmp$rand`";
		tai_mysqlExec($sql);
		//$cli->pause();
		$sql = "drop table `word_tmp$rand`";
		tai_mysqlExec($sql);
		
		//if($pc->i()>10)break;	// debug
	}
	socket_close($socket);
	@mysql_free_result($res);
?>
<?php
	// Get News From Google Reader ( FOR 聯合新聞網 )
	require_once('include.php');
	
	set_time_limit(0);  // 這個程式的執行時間限制
	
	$cfg['proc'] = 1;	// Get News From Google Reader ( FOR 聯合新聞網 )
	$cfg['debug'] = 1;
	
	$baseURL = 'http://www.google.com.tw/reader/api/0/stream/contents/feed/';
	$queryURL = '?r=o&n=1000&client=scroll&ot=';
	$newsAddCnt = 0;
	
	// 清除舊的新聞
	$cli->p("Deleting old news, Please Wait...\n");
	$sql1 = "from `news` where `news_t`<'".date('Y-m-d',time()-7*24*60*60)."'";
	$sql = "delete from `news2word` where `nid` in (select `nid` $sql1)";	// 刪除新聞與 word 的對應表
	tai_mysqlExec($sql);
	$cli->p("在 news2word 刪除了 %d 筆記錄\n",mysql_affected_rows());
	$sql = "delete from `viewlog` where `nid` in (select `nid` $sql1)";	// 刪除新聞 瀏覽紀錄
	tai_mysqlExec($sql);
	$cli->p("在 viewlog 刪除了 %d 筆瀏覽記錄\n",mysql_affected_rows());
	$sql = "delete $sql1";
	tai_mysqlExec($sql);
	$cli->p("在 news 刪除了 %d 筆新聞\n",mysql_affected_rows());
	
	$sql = "select * from `rss` where `proc`='{$cfg['proc']}' order by `rid` ";
	$rssRes = mysql_query($sql);
	while( $rssRow = @mysql_fetch_assoc($rssRes) )
	{
		// 預設設定
		$ot = time()-7*24*60*60;
		
		// 讀取之前的紀錄
		$rssVar = json_decode($rssRow['varible'],1);
		
		while( true )
		{
			if(isset($rssVar['updated']) && $rssVar['updated'] > $ot)
				$ot = $rssVar['updated'];
			$cli->p("Get %s\n",$baseURL . urlencode($rssRow['rss']) . $queryURL . $ot);
			//$cli->pause();
			$rssFeed = json_decode( @file_get_contents( $baseURL . urlencode($rssRow['rss']) . $queryURL . $ot ),1);
			if( !isset($rssFeed) ) die('Can\'t get google reader page. Please check network.');
			$pc = new percent_C(count($rssFeed['items']));
			$pc->p('Get NEWS... %d%% of '.count($rssFeed['items']));
			foreach($rssFeed['items'] as $news)
			{
				$pc->c();
				$news['crawlTimeMsec'] = substr($news['crawlTimeMsec'],0,-3);
				if( $news['crawlTimeMsec'] > $rssFeed['updated'] ) $rssFeed['updated'] = $news['crawlTimeMsec'];
				$news['news_t'] = date('Y-m-d H:i:s',$news['crawlTimeMsec']);
				$news['title'] = @mysql_escape_string($news['title']);
				$news['alternate'][0]['href'] = @mysql_escape_string($news['alternate'][0]['href']);
				
				// 確認網頁是否抓取過
				$sql = "select count(*) as 'cnt' from `news` where `url`='{$news['alternate'][0]['href']}'";
				$res = mysql_query($sql);
				$row = mysql_fetch_assoc($res);
				@mysql_free_result($res);
				if($row['cnt']>0)continue;		// 已有重複
				
				// UDN 聯合新聞網
				usleep(rand(10000,20000));		// 延遲 0.1s ~ 0.2s
				$html = iconv('big5','utf-8//IGNORE',file_get_contents($news['alternate'][0]['href']));
				//echo $html;
				
				$html = preg_match('~<div class="story" id="story">(.*?)</div>~us',$html,$html);
				$html = @mysql_escape_string($html);
				if( $html == '' ) continue;		// 網頁沒內容
				
				$sql = "insert into `news` (title,article,news_t,url,rid) values ('{$news['title']}','$html','{$news['news_t']}','{$news['alternate'][0]['href']}','{$rssRow['rid']}')";
				tai_mysqlExec($sql);
				
				$newsAddCnt++;
			}
			$cli->p("\n");
			$rssVar['updated'] = intval($rssFeed['updated']);
			if( count($rssFeed['items']) < 999 ) break;
		}
		$sql = "update `rss` set `varible` = '".@mysql_escape_string(json_encode($rssVar))."' where `rid` = '{$rssRow['rid']}' ";
		tai_mysqlExec($sql);
	}
	$cli->p("新增了 $newsAddCnt 筆記錄。");
	$sql = "delete from `news` where `article`=''";
	tai_mysqlExec($sql);
	@mysql_free_result($rssRes);
?>
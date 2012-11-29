<?php
	// Get News From Google Reader ( FOR 聯合新聞網 )
	require_once('include.php');
	require_once('library/simple_html_dom.php');
	
	set_time_limit(0);  // 這個程式的執行時間限制
	
	$cfg['proc'] = 1;	// Get News From Google Reader ( FOR 聯合新聞網 )
	$cfg['debug'] = 1;
	
	$baseURL = 'http://www.google.com.tw/reader/api/0/stream/contents/feed/';
	$queryURL = '?r=o&n=1000&client=scroll&ot=';
	$newsAddCnt = 0;
	$dom = new simple_html_dom();
	
	/*
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
	*/
	
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
				$cli->pause();
				$pc->c();
				$news['crawlTimeMsec'] = substr($news['crawlTimeMsec'],0,-3);
				if( $news['crawlTimeMsec'] > $rssFeed['updated'] ) $rssFeed['updated'] = $news['crawlTimeMsec'];
				$news['news_t'] = date('Y-m-d H:i:s',$news['crawlTimeMsec']);
				$news['title'] = @mysql_escape_string($news['title']);
				$news['alternate'][0]['href'] = @mysql_escape_string($news['alternate'][0]['href']);
				
				// 確認網頁是否抓取過
				$cli->p( strstr($news['alternate'][0]['href'],'=') );
				$sql = "select count(*) as 'cnt' from `news` where `url`='{$news['alternate'][0]['href']}'";
				$res = mysql_query($sql);
				$row = mysql_fetch_assoc($res);
				@mysql_free_result($res);
				if($row['cnt']>0)
				{
					$cli->p("URL 重複: {$news['alternate'][0]['href']}\n");
					continue;		// 已有重複
				}
				
				// UDN 聯合新聞網
				usleep(rand(10000,20000));		// 延遲 0.1s ~ 0.2s
				$html = iconv('cp950','utf-8//IGNORE',tai_udnGetNews($news['alternate'][0]['href']));
				//echo $html;
				
				$dom->load($html);
				$html = $dom->find('#story',0)->plaintext;
				$dom->clear();
				$html = @mysql_escape_string($html);
				if( $html == '' )
				{
					$cli->p("該 URL 抓不到內容: {$news['alternate'][0]['href']}\n");
					continue;		// 網頁沒內容
				}
				
				$sql = "insert into `news` (title,article,news_t,url,rid) values ('{$news['title']}','$html','{$news['news_t']}','{$news['alternate'][0]['href']}','{$rssRow['rid']}')";
				//tai_mysqlExec($sql);
				
				$newsAddCnt++;
			}
			$cli->p("\n");
			$rssVar['updated'] = intval($rssFeed['updated']);
			if( count($rssFeed['items']) < 999 ) break;
		}
		$sql = "update `rss` set `varible` = '".@mysql_escape_string(json_encode($rssVar))."' where `rid` = '{$rssRow['rid']}' ";
		//tai_mysqlExec($sql);
	}
	$cli->p("新增了 $newsAddCnt 筆記錄。");
	$sql = "delete from `news` where `article`=''";
	tai_mysqlExec($sql);
	@mysql_free_result($rssRes);
	
	function tai_udnGetNews($url)
	{
		static $referer = 'http://www.udn.com/';
		$ch = curl_init();
		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => 0,
			CURLOPT_VERBOSE => 0,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERAGENT => "Mozilla/4.0 (compatible;)",
			CURLOPT_COOKIEJAR => 'cookie.txt',
			CURLOPT_FOLLOWLOCATION => 1,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTPHEADER => array(
				'accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
				'accept-charset:UTF-8,*;q=0.5',
				'accept-language:zh-TW,zh;q=0.8,en-US;q=0.6,en;q=0.4',
				'cache-control:max-age=0',
				'content-type:application/x-www-form-urlencoded',
				'origin:http://www.udn.com',
				'referer:'.$referer,
				'user-agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.64 Safari/537.11',
			)
		);
		curl_setopt_array($ch, $options);
		$html = curl_exec($ch); 
		
		// 儲存最後的有效網址
		$referer = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		if( curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200 )
			echo 'Can not get html.'.PHP_EOL;
		curl_close($ch);
		return $html;
	}
?>
<?php
	//require_once('library/phpQuery-onefile.php');
	require_once('library/simple_html_dom.php');
	
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
	
	$html = tai_udnGetNews('http://forum.udn.com/forum/NewsLetter/NewsPreview?NewsID=7516358');
	$html = iconv('CP950','UTF-8//IGNORE',$html);
	echo htmlspecialchars($html);
	/* phpQuery
	$pq = phpQuery::newDocument($html);
	$results = $pq['#story']->html();
	
	echo '<pre>'.htmlspecialchars(var_export($results,true)).'</pre>';
	*/
	$dom = new simple_html_dom();
	$dom->load($html);
	echo $dom->find('#story',0)->plaintext;
?>
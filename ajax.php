<?php
// token for test c98fe35ed782c91b928f71481d5d99761ff55969
include_once('include.php');

$req = $_REQUEST;
$safereq = array_map('mysql_real_escape_string',$req);	// prevent sql injection
$data = array();

// 處理 token
global $token;
$token = new session_C();
$token->prefix('nchucsnewsToken_');

if( !empty( $req['token'] ) ) // 將 token 轉換為使用者紀錄
{
	try
	{
		$sql = "select * from `tokens` where `token` = '{$safereq['token']}'";
		$tokenRes = mysql_query($sql);
		$tokenRow = mysql_fetch_assoc($tokenRes);
		@mysql_free_result($tokenRes);
		if( ! $tokenRow )
			throw new Exception('Token invaild.');	// 找不到 token
		$token->uid = $tokenRow['uid'];
	}
	catch(Exception $e)
	{
		$errorMsg = $e->getMessage();
		if(!empty($errorMsg))
			$data['error'][] = $errorMsg;
	}
}

try{
	// You can attach "readnid" at any request
	// 紀錄使用者的閱讀紀錄
	if( $token->uid && !empty($req['readnid']) && preg_match('/^\d+(,\d+)*$/',$req['readnid']) ) // 儲存已讀新聞
	{
		foreach( explode(',',$req['readnid']) as $readnid )
		{
			// 新增閱讀紀錄
			$sql = "select `vid` from `viewlog` where `uid`='{$token->uid}' and `nid`='$readnid' ";
			$vid = tai_mysqlResult($sql);
			if( isset($vid) )
			{
				$sql = "update `viewlog` set `view_t`=now() where `vid`='$vid'";
				tai_mysqlExec($sql);
			}
			else
			{
				$sql = "insert into `viewlog` (`uid`,`nid`) values ('{$token->uid}','$readnid')";
				tai_mysqlExec($sql);
			}
		}
	}

// 開始處理各項需求
	
	switch($req['get'])
	{
	case 'news': // 取得新聞
		// ajax.php?get=news&nid=123&debug=1
		if( preg_match('/^\d+(,\d+)*$/',$req['nid']))
		{
			$sql = "select * from news where nid in ({$req['nid']})";
			if(! $newsRes = mysql_query($sql))
				throw new Exception(mysql_error());
			$data['newsCnt'] = mysql_num_rows($newsRes);
			while( $newsRow = mysql_fetch_assoc($newsRes) )
				$data['news'][] = $newsRow;
			@mysql_free_result($newsRes);
		}
		else throw new Exception('The news nid incorrect.');
		break;
	case 'rss': // 取得新聞分類
	case 'category':
		// ajax.php?get=rss&debug=1
		// rss 
		if( isset($req['rid']) && !preg_match('/^\d+(,\d+)*$/',$req['rid']))
			throw new Exception('The '.$req['get'].' rid incorrect.');
		$sql = "select * from `rss`";
		if(isset($req['rid'])) $sql .= "where rid in ({$req['rid']})";
		if(! $rssRes = mysql_query($sql))
			throw new Exception(mysql_error());
		$data['rssCnt'] = mysql_num_rows($rssRes);
		while( $rssRow = mysql_fetch_assoc($rssRes) )
		{
			$rssRow['varible'] = json_decode($rssRow['varible'],true);
			$data['rss'][] = $rssRow;
		}
		@mysql_free_result($rssRes);
		break;
	case 'cnt':
		// ajax.php?get=cnt&group=rid&debug=1
		// ajax.php?get=cnt&group=date&debug=1
		// ajax.php?get=cnt&rid=1&debug=1
		// ajax.php?get=cnt&date=2012-12-01&debug=1
		if( ! $dck->date($req['date']) )
			unset($req['date']);
		if( ! $dck->uint($req['rid']) )
			unset($req['rid']);
		if( !empty($req['group']) && !in_array($req['group'], array('rid','date')) )
			unset($req['group']);
		if( !( empty($req['date']) ^ empty($req['rid']) ) )
		{
			if( !empty($req['date']) )
				throw new Exception('You can not assign date and rid at the same time.');
			else if(empty($req['group']))
				throw new Exception('Need group argument.');
		}
		
		if(isset($req['date']))
			$sql = "select `rid`,count(*) as 'cnt' from `news` where left(`news_t`,10)='{$req['date']}' group by `rid` ";
		else if(isset($req['rid']))
			$sql = "select * from (select left(`news_t`,10) as 'date', count(*) as 'cnt' from `news` where `rid`='{$req['rid']}' group by `date`) as a order by a.date desc ";
		else if($req['group']=='date')
			$sql = "select * from (select left(`news_t`,10) as 'date',count(*) as 'cnt' from `news` group by left(`news_t`,10)) as a order by a.date desc ";
		else $sql = "select `rid`,count(*) as 'cnt' from `news` group by `rid`";
		
		$res = mysql_query($sql);
		$data['cntCnt'] = mysql_num_rows($res);
		while( $row = mysql_fetch_assoc($res) )
			$data['cnt'][] = $row;
		@mysql_free_result($res);
		break;
	case 'today': // 取得今日推薦清單
		// ajax.php?get=today&debug=1&token=c98fe35ed782c91b928f71481d5d99761ff55969&limit=1
		if( !$token->uid )	// 未登入，不給使用
			throw new Exception('Need token.');
		$get = $_GET;
		$checkGet = array( // 預設值
			'simi_1st' => array( 'pattern' => '/^\d+$/', 'default' => 60 ),
			'simi_2st' => array( 'pattern' => '/^\d+$/', 'default' => 30 ),
			'simi_3st' => array( 'pattern' => '/^\d+$/', 'default' => 10 ),
			'onto_limit' => array( 'pattern' => '/^\d+$/', 'default' => 100 ),
			'limit' => array( 'pattern' => '/^\d+$/', 'default' => 1000 ),	// 新增限制
		);
		foreach( $checkGet as $k => $o )
			if( !preg_match( $o['pattern'], $get[$k]) )
				$get[$k] = $o['default'];
		
		// 此段程式碼直接複製 news.php。
		// 新增一個暫時的 TABLE，以便處理新聞
		$rand = rand(0,255);
		$sql = "create temporary table `today_$rand` select `nid` from `news` limit 0;";
			//echo $sql.'<br />';
		tai_mysqlExec($sql);
		
		// 加入同好者推薦
		// 選出前三大同好者
		$sql = "select `uid` from ( select `uid2` as 'uid',`simi` from similarity where `uid1` = '{$token->uid}' union select `uid1`,`simi` from similarity where `uid2` = '{$token->uid}' ) as a order by a.simi desc limit 3";
			//echo $sql.'<br />';
		$simiRes = mysql_query($sql);
		for( $i=1; $i<=3 && $simiRow = mysql_fetch_row($simiRes); $i++ )
		{
			// 對每個同好者，取其瀏覽紀錄，存進暫時的 TABLE
			$sql = "insert into `today_$rand` select `nid` from `viewlog` where `uid` = '{$simiRow[0]}' order by `view_t` desc limit ".$get["simi_{$i}st"];
			//echo $sql.'<br />';
			tai_mysqlExec($sql);
		}
		@mysql_free_result($simiRes);
		
		// 加入個人化推薦
		// 取出 ontology TABLE 的推薦結果，存進暫時的 TABLE
		$sql = "insert into `today_$rand` select `nid` from `ontology` where `uid` = '{$token->uid}' order by `weight` desc limit {$get['onto_limit']}";
			//echo $sql.'<br />';
		tai_mysqlExec($sql);
		
		// 如果要增加新的推薦方法，一樣從這裡繼續加下去
		
		// 開始對暫時 TABLE 中的新聞做處理
		// 先濾除使用者已看過的新聞，再依照時間排序
		$sql = "select distinct c.nid,c.title,c.`news_t` from `today_$rand` as a left join `viewlog` as b on b.uid='{$ses->uid}' and a.nid=b.nid left join `news` as c on a.nid=c.nid where b.nid is null order by `news_t` desc limit {$get['limit']}";	// 新增限制
		//echo $sql.'<br />';
		$rcmdRes = mysql_query($sql);
		$data['listCnt'] = mysql_num_rows($rcmdRes);
		$data['listType'] = 'today';
		while( $rcmdRow = mysql_fetch_assoc($rcmdRes) )
			$data['list'][] = $rcmdRow;
		@mysql_free_result($rcmdRes);
		break;
	case 'list':
		// ajax.php?get=list&debug=1&rid=2&date=2012-12-24&limit=1
		// ajax.php?get=list&debug=1&rid=2&limit=1
		// ajax.php?get=list&debug=1&date=2012-12-24&limit=1
		$get = $_GET;
		if( !$dck->uint($get['rid']) )
			unset($get['rid']);
		if( !$dck->date($get['date']) )
			unset($get['date']);
		
		$listType = isset($get['date'])*2 + isset($get['rid']);
		if( $listType == 0 ) // 必須要指定任何一個
			throw new Exception('Need rid or date.');
		$listTypeArray = array('', 'category', 'date', 'categoryAndDate');
		$data['listType'] = $listTypeArray[$listType];
		if( !preg_match('/^\d+$/',$get['limit']) )
			$get['limit'] = 1000;
		
		// 組合 sql
		$sql_where = array();
		if(isset($get['date'])) $sql_where[] .= " LEFT(`news_t`,10)='{$get['date']}' ";
		if(isset($get['rid'])) $sql_where[] .= " `rid`='{$get['rid']}' ";
		$sql = "select `nid`,`title`,`news_t` from `news` where ".implode('and',$sql_where)." order by `news_t` desc limit {$get['limit']}";
		$newsRes = mysql_query($sql);
		$data['listCnt'] = mysql_num_rows($newsRes);
		while( $newsRow = mysql_fetch_assoc($newsRes) )
			$data['list'][] = $newsRow;
		@mysql_free_result($newsRes);
		break;
	default:
		throw new Exception('The get is not support.');
		break;
	}
}
catch( Exception $e )
{
	$data['error'][] = $e->getMessage();
	// error 必為 array
}

// 強制全部使用 String 以方便 Android 使用
function forceString($v)
{
	if(is_array($v))
		return array_map('forceString',$v);
	if(is_string($v))
		return $v;
	return $v.'';	
}
$data = array_map('forceString',$data);

// 設置輸出選項
$jsonOpt = JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES;
if(intval($req['pretty'])!=0 || intval($req['debug'])==1) $jsonOpt |= JSON_PRETTY_PRINT;
if(intval($req['unescaped_unicode']!=0)) $jsonOpt ^= JSON_UNESCAPED_UNICODE;

if(intval($req['debug'])==1)
	die('<pre>'.htmlspecialchars(json_encode($data,intval($jsonOpt))).'</pre>');
else die(json_encode($data,intval($jsonOpt)));


?>
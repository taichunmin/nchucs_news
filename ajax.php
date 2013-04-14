<?php
include_once('include.php');

$req = $_REQUEST;
$data = array();

try{
	switch($req['get'])
	{
	case 'news':
		/*
		if( !$ses->uid )	// 未登入，不給使用
			throw new Exception('Need login.');
		*/
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
	case 'rss':
	case 'category':
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
	default:
		throw new Exception('The act is not support.');
		break;
	}
}
catch( Exception $e )
{
	$data['error'][] = $e->getMessage();
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
	die('<pre>'.htmlspecialchars(json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT)).'</pre>');
else die(json_encode($data,intval($jsonOpt)));


?>
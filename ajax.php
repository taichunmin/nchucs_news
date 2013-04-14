<?php
include_once('include.php');

$req = $_REQUEST;
$data = array();

try{
	switch($req['get'])
	{
	case 'news':
		if( !$ses->uid )	// 未登入，不給使用
			throw new Exception('Need login.');
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

die('<pre>'.htmlspecialchars(var_export($data,true)).'</pre>');
//die(json_encode($data));

?>
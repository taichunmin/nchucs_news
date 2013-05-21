<?php
/*
 *
 *	使用 POST 的方式，與伺服器交換取得 Token，以便於 ajax.php 程式中使用
 *	由於目前只是為了比賽，所以只先使用簡單的認證方法。
 *
 */
require('include.php');
$data = array();

try
{
	// 清除過期的 token
	$sql = "delete from `tokens` where (now()-`ts`) > 2592000";
	tai_mysqlExec($sql);
	
	// escape string
	$post = array_map('mysql_real_escape_string', $_POST); // _REQUEST
	
	// email and password check
	if( !isset($post['email']) || empty($post['email']) )
		throw new Exception('Please provide email.');
	if( !isset($post['pass']) || empty($post['pass']) )
		throw new Exception('Please provide password.');
	$sql = "select * from `user` where `email` = '{$post['email']}' limit 1";
	if( ! $userRes = mysql_query($sql) )
		throw new Exception('SQL error.');
	if( ! $userRow = mysql_fetch_assoc($userRes) )
		throw new Exception('Email or Password ERROR.');
	@mysql_free_result($userRes);
	if( $userRow['pass'] != sha1($post['pass']) )
		throw new Exception('Email or Password ERROR.');
	
	// 記錄登入訊息
	$sql = "update `user` set `login_t`=now() where `uid`='{$ses->uid}'";
	tai_mysqlExec($sql);
	
	// 避免頻繁取 token ( 信任的 IP 除外 )
	if( !in_array($_SERVER['REMOTE_ADDR'],array(
		'127.0.0.1',
	)) )
	{
		$sql = "select * from `tokens` where `ip` = '{$_SERVER['REMOTE_ADDR']}' and (now()-`ts`) < 60";
		$tscheckRes = mysql_query($sql);
		if( mysql_num_rows($tscheckRes)>0 )
			throw new Exception('You have to wait for a while to get another token.');
		@mysql_free_result($tscheckRes);
	}
	
	// 產生 Token，並將資訊存回資料庫
	global $token;
	$token = new session_C();
	$token->prefix('nchucsnewsToken_');
	$token->uid = $userRow['uid'];
	$tokenCnt = 0;
	while( true )
	{
		$token->token = sha1('nchucsnewsToken_'.$userRow['uid'].'_'.time().rand(1,1000));
		$sql = "insert into `tokens` values(NULL, '{$token->uid}', '{$token->token}', '{$_SERVER['REMOTE_ADDR']}', NULL)";
		if( mysql_query($sql) ) break;
		$tokenCnt++;
		if($tokenCnt>100) throw new Exception('Generate token error.');
	}
	$data['uid'] = $token->uid;
	$data['token'] = $token->token;
}
catch( Exception $e )
{
	$data['error'][] = $e->getMessage();
	// error 必為 array
}
$token->clear();	// 清空 token 紀錄
die(json_encode($data));
?>

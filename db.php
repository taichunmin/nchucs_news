<?php
	// 設定不同主機資料庫之設定
	switch($_SERVER['HTTP_HOST'])
	{
	default:
		$cfg['dbHost'] = 'localhost';
		$cfg['dbUser'] = 'nchucsnews';
		$cfg['dbPass'] = 'Enter Your Password Here';
		$cfg['dbDatabase'] = 'nchucsnews';
		// mysqldump -u root -p nchucsnews > db.sql
		break;
	}
	
	// 新增 ckipsvr 的帳號密碼，需要去註冊
	$cfg['ckipsvr'] = array(
		'username' => 'taichunmin',
		'password' => 'Enter Your Password Here',
	);
?>
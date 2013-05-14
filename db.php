<?php
	// 設定不同主機資料庫之設定
	switch($_SERVER['HTTP_HOST'])
	{
	default:
		$cfg['dbHost'] = 'localhost';
		$cfg['dbUser'] = 'nchucsnews';
		$cfg['dbPass'] = 't5KthfzGKpts4ctc';
		$cfg['dbDatabase'] = 'nchucsnews';
		// mysqldump -u root -p nchucsnews > db.sql
		break;
	}
?>
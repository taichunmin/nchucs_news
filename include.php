<?php
	// nchucs_news include.php file
	ob_start();
	session_start();
	header('Content-Type: text/html; charset=utf-8');
	mb_internal_encoding("UTF-8");
	date_default_timezone_set('Asia/Taipei');
	$cfg = array(
		'prefix' => 'nchucsnews_',
		'state' => md5(uniqid(rand(), TRUE)), 
		'adminEmail' => 'taichunmin@gmail.com',
		'today' => date('Y-n-j',time()),	// 今天日期
		'pageLimit' => 30,
		'pageListSize' => 7,
		'debug' => 1,
	);
	// 設定不同主機資料庫之設定
	switch($_SERVER['HTTP_HOST'])
	{
	case '127.0.0.1':
	default:
		$cfg['dbHost'] = 'localhost';
		$cfg['dbUser'] = 'nchucsnews';
		$cfg['dbPass'] = 't5KthfzGKpts4ctc';
		$cfg['dbDatabase'] = 'nchucsnews';
		// mysqldump -u root -p nchucsnews > db.sql
		break;
	case '140.120.15.146':
	case 'dmlab.cs.nchu.edu.tw':
		$cfg['dbHost'] = 'localhost';
		$cfg['dbUser'] = 'newsrecommender';
		$cfg['dbPass'] = 'news@)!@';
		$cfg['dbDatabase'] = 'newsrecommender';
		// mysql -u newsrecommender -p newsrecommender < db.sql
		break;
	}
	function tai_mysqlConnect()	// 連接資料庫
	{
		global $cfg;
		if(!mysql_connect($cfg['dbHost'],$cfg['dbUser'],$cfg['dbPass']))
		{
			if($cfg['debug'])die( mysql_error());
			else die("Connect mysql failed! 1");
		}
		@mysql_set_charset('utf8');	// 設定字元集宇連線校對
		@mysql_query('SET CHARACTER_SET_CLIENT=utf8');
		@mysql_query('SET CHARACTER_SET_RESULTS=utf8');
		if(@mysql_select_db($cfg['dbDatabase'])==0)
			die("Select database failed! 2");
	}
	function tai_location($url="?")
	{
		header('Location: '.$url);
		exit(0);
	}
	function tai_dbUser($name,$value=null,$uid=null)
	{
		global $cfg,$ses;
		if( !isset($uid) && !$ses->uid)
			return '未登入';
		$name = @mysql_escape_string($name);
		if(!isset($uid))
			$uid = $ses->uid;
		if(isset($value))
		{
			$value = @mysql_escape_string($value);
			$sql = "update `user` set `$name`='$value' where `uid`='$uid' limit 1";
			tai_mysqlExec($sql);
			return $value;
		}
		else
		{
			$sql = "select `$name` from `user` where `uid`='$uid' limit 1";
			if(!($res=@mysql_query($sql)) && $cfg['debug'])
				die('Mysql error: '.mysql_error());
			$row = mysql_fetch_assoc($res);
			@mysql_free_result($res);
			return $row[$name];
		}
	}
	function tai_mysqlResult($sql)
	{
		$res = mysql_query($sql);
		if(!$res) return NULL;
		$row = mysql_fetch_row($res);
		@mysql_free_result($res);
		if(!$row) return NULL;
		return $row[0];
	}
	function tai_mysqlExec($sql)
	{
		global $cfg;
		if(!@mysql_query($sql))
		{
			if($cfg['debug']==1)
				die('Mysql error: '.$sql." <br />\n".mysql_error());
			else die('Mysql error. Please inform the server admin.');
		}
	}
	tai_mysqlConnect();
	/*
	名稱：自動 include 物件
	
	版本：
		2012/08/09		taichunmin		初版
		2012/10/01		taichunmin		新增優先讀取名單 order
	
	用途：
		1.可以自動 include 在某個資料夾下的 *.class.php 檔案
		2.注意 class 檔案中的任何宣告都須使用 global，才不會在 include 後消失。
		3.有優先必要的 class，可將名字打入 order.php 檔案內，一行一個。
	*/
	function tai_autoIncludeClass($path)
	{
		if( ! is_dir($path) ) return false;
		if( ! preg_match('/\/$/',$path))
			$path .= '/';
		if(file_exists($path.'order.php'))
		{
			$order = explode("\r\n",file_get_contents($path.'order.php'));
			foreach($order as $cname)
				if(strlen($cname)>0 && file_exists($path.$cname.'.class.php'))
					include_once($path.$cname.'.class.php');
		}
		$handle = opendir($path);
		while (false !== ($fname = readdir($handle)))
		{
			if(preg_match('/\.class\.php$/',$fname))
			{
				//echo $path.$fname;
				include_once($path.$fname);
			}
		}
		closedir($handle);
	}
	tai_autoIncludeClass('class');
	/* debug
	*/
?>
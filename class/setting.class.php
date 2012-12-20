<?php
/**
 *	使用者設定檔的物件
 *
 *	@author		taichunmin <taichunmin@gmail.com>
 *	@date		2012-12-20
 *	@package	nchucs_news
 */
class setting_C
{
	private $d;
	private $defau = array(
		'simi_1st' => '60',
		'simi_2st' => '30',
		'simi_3st' => '10',
	);
	public function __construct()
	{
		global $ses;
		if(empty($ses->uid))
		{
			$this->d = null;
		}
		else
		{
			$sql = "select `setting` from `user` where `uid`='{$ses->uid}'";
			$this->d = json_decode(tai_mysqlResult($sql),1);
			if($this->d == null)
				$this->d = array();
		}
	}
	public function __destruct()
	{
		global $ses;
		if(!isset($this->d)) return ;
		$json = @mysql_escape_string(json_encode($this->d));
		$sql = "update `user` set `setting` = '$json' where `uid` = '{$ses->uid}'";
		tai_mysqlExec($sql);
	}
	public function post()
	{
		if(!isset($this->d)) return ;
		foreach($_POST as $k => $v)
		{
			switch($k)
			{
			case 'simi_1st':
			case 'simi_2st':
			case 'simi_3st':
				if($dck->uint($v))
					$this->d[$k] = $v;
				break;
			}
		}
	}
	public function __get($k)
	{
		if(!isset($this->d)) return null;
		if(isset($this->d[$k]))
			return $this->d[$k];
		if(isset($this->defau[$k]))
			return $this->defau[$k];
		return null;
	}
	public function __set($k, $args)
	{
		if(!isset($this->d)) return ;
		$this->d[$k] = $args[0];
	}
	public function __isset($k)
	{
		if(!isset($this->d)) return ;
		return isset($this->d[$k]);
	}
	public function __unset($k)
	{
		if(!isset($this->d)) return ;
		unset($this->d[$k]);
	}
}
global $stt;
$stt = new setting_C;
?>
<?php
/*
	名稱：資料欄位檢查
	
	版本：
		2012/08/09		taichunmin		初版，debug 未完成
	
	用途：
		資料檢查。
		直接以屬性取得該 pattern
		呼叫成函式，直接利用該 pattern 進行 preg_match，可比較多筆資料
		
	用法：
		請參照程式碼底端的 debug 區域
*/
	class dataCheck_C
	{
		private $pattern = array(
			'email' => '/^[\w.]+@[\w.]+(\.[\w.]+)+$/',
			'int' => '/^(\+|-)?\d+$/',
			'uint' => '/^\+?\d+$/',
			'double' => '/^(\+|-)?\d+(?:\.\d*)?$/',
			'udouble' => '/^\+?\d+(?:\.\d*)?$/',
			'date' => '/^(19|20)\d{2}-(0?[1-9]|1[0-2])-(0?[1-9]|[1-2][0-9]|3[0-1])$/',
			'url' => '/^(http|https|ftp)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(:[a-zA-Z0-9]*)?\/?([a-zA-Z0-9\-\._\?\,\'\/\\\+&%\$#\=~])*[^\.\,\)\(\s]$/',
			'username' => '/^[\w.]+$/',
		);
		private $debug;
		
		public function debug($debug = null)
		{
			if(isset($debug))
				return intval($this->debug = !empty($debug));
			else return intval($this->debug);
		}
		
		public function __construct()
		{
			global $cfg;
			$this->debug($cfg['debug']);
		}
		
		public function __call($k, $args)
		{
			if(! $this->__isset($k))	// 沒有該規則，直接回傳 null
				return null;
			$res = true;
			foreach($args as $v)
				if( !@preg_match($this->pattern[$k], $v) )
				{
					$res = false;
					break;
				}
			return $res;
		}
		
		public function __set($k, $v)
		{
			$this->pattern[$k] = $v;
		}
		
		public function __get($k)
		{
			if(! $this->__isset($k))
				return null;
			return $this->pattern[$k];
		}
		
		public function __unset($k)
		{
			if($this->__isset($k))
				unset($this->pattern[$k]);
		}
		
		public function __isset($k)
		{
			return array_key_exists($k, $this->pattern);
		}
	};
	global $dck;
	$dck = new dataCheck_C;
/*
	debug
	echo intval($dck->email('taichunmin@gmail.com','a@b.c'));
	echo intval($dck->email('taichunmingmail.com'));
	echo intval($dck->double('-123.456'));
	echo intval($dck->url('http://taichunmin.pixnet.net/blog'));
	echo $dck->username = '/^[\w._]+$/';
	echo $dck->username('tai.chunmin');
	echo $dck->debug();
	echo $dck->debug('debug');
*/
?>
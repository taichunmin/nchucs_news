<?php
/*
	名稱：資料欄位檢查
	
	版本：
		2012/08/09		taichunmin		初版，debug 未完成
		2012/12/20		taichunmin		模仿 jQuery Vaildate 修改程式
	
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
			'required' => '/^.*$/us',
			'email' => '/^[\w.]+@[\w.]+(\.[\w.]+)+$/us',
			'int' => '/^(\+|-)?\d+$/us',
			'uint' => '/^\+?\d+$/us',
			'double' => '/^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/us',
			'udouble' => '/^\+?\d+(?:\.\d*)?$/us',
			'dateISO' => '/^\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}$/us',
			'url' => '/^(http|https|ftp)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(:[a-zA-Z0-9]*)?\/?([a-zA-Z0-9\-\._\?\,\'\/\\\+&%\$#\=~])*[^\.\,\)\(\s]$/us',
			'username' => '/^[\w.]+$/us',
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
			$preg_check = true;
			$ret = false;
			switch($k)
			{
			case 'minlength':
				if(! $this->uint($args[1]) ) die('$k args error!');
				$this->__set('minlength','/^.{'.$args[1].',}$/us');
				break;
			case 'maxlength':
				if(! $this->uint($args[1]) ) die('$k args error!');
				$this->__set('maxlength','/^.{,'.$args[1].'}$/us');
				break;
			case 'rangelength':
				if( ! $this->uint($args[1]) || !$this->uint($args[2]) ) die('$k args error!');
				$this->__set('rangelength','/^.{'.$args[1].','.$args[2].'}$/us');
				break;
			case 'min':
				if(!$this->int($args[1])) die('$k args error!');
				return $this->int($args[0]) && $args[0]>=$args[1];		// 直接回傳
			case 'max':
				if(!$this->int($args[1])) die('$k args error!');
				return $this->int($args[0]) && $args[0]<=$args[1];		// 直接回傳
			case 'range':
				if( ! $this->uint($args[1]) || !$this->uint($args[2]) ) die('$k args error!');
				return $this->int($args[0]) && $args[0]>=$args[1] && $args[0]<=$args[2];		// 直接回傳
			case 'date':
				return strtotime($args[0]) !== false;
			}
			if(! $this->__isset($k)) return null;	// 沒有該規則，直接回傳 null
			return @preg_match($this->pattern[$k], $v);
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
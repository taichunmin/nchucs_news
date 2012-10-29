<?php
/*
	名稱：Session 控管 Class
	
	版本：
		2012/07/16		taichunmin		使用 prefix 來控管版本、prefix 來更改前贅詞
		2012/08/07		taichunmin		增加 msg 相關函數 (hasMsg、msg、clearMsg)
	
	用途：
		有使用這個 Class 的系統，可以有效的避免彼此 Session 之間受到汙染。
		
	用法：
		請參照程式碼底端的 debug 區域
*/
class session_C
{
	private $_prefix = '';	// 前贅詞，避免 session 衝突
	private $_prefix_len = 0;
	private $_msgName = 'msg';
	
	// constructor
	public function __construct()
    {
		global $cfg;
		if(!empty($cfg['prefix']))
		{
			$this->_prefix = $cfg['prefix'];
			$this->_prefix_len = strlen($this->_prefix);
		}
    }
	public function debug()
	{
		global $cfg;
		if(empty($cfg['debug']))return;
		
		echo "prefix={$this->_prefix}<br />";
		echo '<pre>';
		print_r($_SESSION);
		echo '</pre>';
	}
	/**
	 * prefix 設定/取得
	 *
	 * 可用來取得目前的 prefix，或者是指定 prefix，並且是否轉換已儲存的變數。
	 *
	 * @prarm	string	$value	如果有傳入這個參數，則會進行設定 prefix 的動作。
	 * @prarm	bool	$rename	如果要進行設定 prefix 時，設定為 1 可自動將舊 prefix 的變數重新命名。
	 * @return	string	$prefix	回傳 prefix
	 * @see		$ses->prefix()
	 * @see		$ses->prefix('aaa_')
	 * @see		$ses->prefix('aaa_',1)
	 */
	public function prefix($value=null, $rename=0)
	{
		if($value!=null)
		{
			if($rename==1)
			{
				foreach($_SESSION as $k => $v)
					if(substr($k, 0, $this->_prefix_len) == $this->_prefix)
					{
						$_SESSION[ $value . substr($k, $this->_prefix_len) ] = $_SESSION[$k];
						unset($_SESSION[$k]);
					}
			}
			$this->_prefix = $value;
			$this->_prefix_len = strlen($this->_prefix);
		}
		return $this->_prefix;
	}
	public function clear()
	{
		foreach($_SESSION as $k => $v)
			if( substr($k, 0, $this->_prefix_len) == $this->_prefix)
				unset($_SESSION[$k]);
	}
	
	public function __set($name, $value)	// magic funciton
    {
        $_SESSION[$this->_prefix.$name] = $value;
    }

    public function __get($name)	// magic funciton
    {
        if (isset($_SESSION[$this->_prefix.$name])) {
            return $_SESSION[$this->_prefix.$name];
        }
		/*
        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
		*/
        return null;
    }
    public function __isset($name)	// magic funciton
    {
        return isset($_SESSION[$this->_prefix.$name]);
    }
    public function __unset($name)	// magic funciton
    {
        unset($_SESSION[$this->_prefix.$name]);
    }
	
	// 檢查 MSG 中有沒有資料
	public function hasMsg()
	{
		return $this->__isset($this->_msgName);
	}
	/*
		利用此函式來取得 MSG 的內容。
		若有指定 $newMsg，則會自動加在後面。
	*/
	public function msg($newMsg = null)
	{
		if( isset( $newMsg ))
		{
			$this->__set( $this->_msgName, $this->__get($this->_msgName) ."\n\n". $newMsg );
			return 1;
		}
		return str_replace("\n",'\\n',$this->__get($this->_msgName));
	}
	// 清除 MSG 的內容
	public function clearMsg()
	{
		$this->__unset($this->_msgName);
	}
};
global $ses;
$ses = new session_C;
/*
echo $ses->msg('123');
echo $ses->msg('456');
echo intval($ses->hasMsg());

<?php
	if($ses->hasMsg())
	{
		echo '<script>$(function(){alert(\''. $ses->msg() .'\');});</script>';
		$ses->clearMsg();
	}
?>
*/
?>
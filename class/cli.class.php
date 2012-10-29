<?php
/*
Program:
	為了給予 cli 下的程式專用的函數包
	1. printf
		$cli->p(); // 使用方法同 printf
		$cli->vp(); // 可使用 $format,  $args 來呼叫
	2. timer
		Usage:
			a. 使用 startTimer() 來取得 timer id
			b. 使用 interTime( $timerid ) 來取得到現在所花費的時間 ( 人可讀懂的文字 )
			b. 使用 interTS( $timerid ) 來取得到現在所花費的時間 ( 毫秒 )
	3. pause
		自動暫停，等候接收到一個 Enter 鍵
Version:
	2012/10/17		taichunmin		v 1.0
*/
class cli_C
{
	// private varible
	private $_cli_mode;			// 儲存是否為 cli 模式
	private $_output_fout;		// 決定是否使用 $fout 來輸出
	private $_out_iconv = false;		// 決定是否經過 iconv
	private $fout = null;				// 儲存 $fout
	private $eol;
	private $unit  = array( ' ms',' s',' m',' h',' d');
	private $carry = array( 1000, 60,  60,  24,  1000000);
	
	// public varible
	public $timer;
	public $pause_disable = false;
	public $iconv_encode = array('UTF-8','BIG5//IGNORE');
	
	public function __construct($fout=null)
	{
		switch( PHP_SAPI )
		{
		case 'cli':
		case 'cgi-fcgi':
			$this->_cli_mode = true;
			if(PHP_OS == 'Linux') $this->eol = "\n";
			else $this->eol = "\r\n";
			break;
		default:
			$this->_cli_mode = false;
			$this->eol = '<br />';
			break;
		}
		$this->setFout($fout);				// 設定預設輸出串流
		//$this->debug();
	}
	public function cliOnly()
	{
		if( !$this->_cli_mode  )
			die('This program can only run in console mode.');
	}
	public function p()
	{
		$num_args = func_num_args();
		if($num_args==0) return false;
		$args = func_get_args();
		$format = array_shift($args);
		$this->vp($format, $args);
	}
	public function vp($format, $args = null)
	{
		$out = vsprintf($format, $args);
		if($this->_out_iconv)
			$out = iconv($this->iconv_encode[0],$this->iconv_encode[1],$out);
		if($this->_output_fout)
			fwrite($this->fout, $out);
		else print $out;
	}
	public function setFout($fout=null)
	{
		$this->_output_fout = false;
		$this->_out_iconv = true;
		if(isset($fout))
		{
			$this->fout = $fout;
			$this->_output_fout = true;
			$this->_out_iconv = false;
		}
		else $this->fout = STDOUT;
		if($this->_cli_mode)
			$this->_output_fout = true;
		else $this->_out_iconv = false;
	}
	public function getFout() { return $this->fout; }
	public function debug()
	{
		var_dump($this);
	}
	public function startTimer()
	{
		$this->timer[]=$this->_microtime();
		$timerid = count( $this->timer ) -1;
		return $timerid;
	}
	public function interTime($timerid = null)
	{
		// 這個函式回傳人可以讀懂的時間文字
		if(!isset($timerid)) $timerid = count($this->timer)-1;
		$interval = $this->_time_interval( $this->timer[$timerid] );
		$tmp[0] = $interval[1];
		$tmp[1] = $interval[0];
		for( $i=1; $i<4; $i++ )
		{
			$tmp[$i+1] = floor( $tmp[$i] / $this->carry[$i] );
			$tmp[i] %= $this->carry[$i];
		}
		for( $i=4; $i>=0; $i--)
			if($tmp[$i]!=0)
			{
				$res = intval($tmp[$i]).$this->unit[$i];
				if($i>0) $res .= ' '.intval($tmp[$i-1]).$this->unit[$i-1];
				return $res;
			}
	}
	public function interTS($timerid = null)
	{
		// 這個函式回傳毫秒間隔
		if(!isset($timerid)) $timerid = count($this->timer)-1;
		$interval = $this->_time_interval( $this->timer[$timerid] );
		return (empty($interval[0])?'':$interval[0]).$interval[1];
	}
	private function _time_interval( $tm1, $tm2=null )
	{
		if(!isset($tm2)) $tm2 = $this->_microtime();
		else if( $tm1[0] > $tm2[0] || ($tm1[0] == $tm2[0] && $tm1[1] > $tm2[1]))
		{
			$tm3 = $tm1;
			$tm1 = $tm2;
			$tm2 = $tm3;
		}
		$res[0] = $tm2[0] - $tm1[0];
		$res[1] = $tm2[1] - $tm1[1];
		if($res[1]<0)
		{
			$res[1] += 1000;
			$res[0]--;
		}
		return $res;
	}
	private function _microtime()
	{
		$t = explode(' ',microtime());
		return array($t[1],floor($t[0]*1000));
	}
	public function pause()
	{
		if( ! $this->_cli_mode || $this->pause_disable ) return;
		fflush(STDIN);
		$this->p(" Press ENTER to continue...");
		fgets(STDIN,1000);
	}
}
global $cli;
$cli = new cli_C();

/*
$cli->p("%d%%\n",60);
$cli->pause();
$cli->p("%s\n",PHP_SAPI);
$tm1 = $cli->startTimer();
usleep(500000);
$cli->p($cli->interTS($tm1));
//$cli->debug();
*/
?>
<?php
/*
功能：計算百分比

版本：
	2012/10/03		taichunmin		First Release
*/

class percent_C
{
	private $max = 1;
	private $cnt = 0;
	private $denominator;	// 分母
	private $molecular = 0;	// 分子
	private $ng;			// 下一個目標
	private $clear_line = "\r                                                                               \r";
	public $printFormat;
	public function __construct($max, $denominator=100)
	{
		if($max<1)$max=1;
		$this->max = $max;
		$this->denominator = $denominator;
		$this->ng = $this->cnt = $this->molecular = 0;
		$this->printFormat = '';
		$this->_nextGoalCompute();
	}
	private function _nextGoalCompute()
	{
		$this->ng = floor($this->max*($this->molecular+1)/$this->denominator);
	}
	public function c()
	{
		$this->cnt++;
		if($this->cnt > $this->max)
		{
			$this->molecular = $this->denominator;
			$this->p();
			return false;
		}
		if($this->cnt >= $this->ng)
		{
			$this->molecular = $this->cnt / $this->max * $this->denominator;
			$this->_nextGoalCompute();
			$this->p();
		}
		return $this;
	}
	public function g()
	{
		return $this->molecular;
	}
	public function i()
	{
		return $this->cnt;
	}
	public function p($f=null)
	{
		if(isset($f))$this->setf($f);
		if( empty($this->printFormat) ) return;
		fprintf( STDOUT, $this->clear_line.$this->printFormat, $this->molecular );
		return $this;
	}
	public function setf($f)
	{
		$this->printFormat = str_replace(array("\n","\r"),'',$f);
		return $this;
	}
	public function debug()
	{
		var_dump($this);
	}
}
?>
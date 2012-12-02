<?php
include_once('include.php');
class similarity_C
{
	private function uid_check_order( &$uid1, &$uid2 )
	{
		if( $uid1 > $uid2 )
		{
			$tmp = $uid1;
			$uid1 = $uid2;
			$uid2 = $tmp;
		}
	}
	public function set( $uid1=0, $uid2=0, $similarity=0 )
	{
		if( !is_numeric( $uid1 ) || !is_numeric( $uid2 ) )
			return false;
		if( $uid1<1 || $uid2<1 )
			return false;
		if( !is_float($similarity) )
			return false;
		if( $similarity < 0 || $similarity > 1 )
			return false;
		$this->uid_check_order($uid1, $uid2);
		$sql = "select * from `similarity` where `uid1`='$uid1' and `uid2`='$uid2' ";
		$res = mysql_query($sql);
		if( mysql_fetch_assoc($res) )
		{
			if( $similarity == 0 )
				$sql = "delete from `similarity` where `uid1`='$uid1' and `uid2`='$uid2' ";
			else $sql = "update `similarity` set `simi` = $similarity where `uid1`='$uid1' and `uid2`='$uid2' ";
			tai_mysqlExec($sql);
		}
		else if( $similarity != 0 )
		{
			$sql = "insert into `similarity` values (NULL, $uid1, $uid2, $similarity)";
			tai_mysqlExec($sql);
		}
		if( mysql_errno() != 0 )
			return false;
		return true;
	}
	public function get( $uid1=0, $uid2=0 )
	{
		if( $uid1 == 0 && $uid2 == 0 )
			return NULL;
		else if( $uid2 == 0 )
		{
			$sql = "select `uid` from `user` where `uid` != '$uid1' order by `uid`";
			$res = mysql_query($sql);
			$uid = array();
			while( $row = mysql_fetch_assoc($res) )
				$simi[ $row['uid'] ] = 0;
			@mysql_free_result($res);
			$sql = "select * from `similarity` where `uid1` = '$uid1' or `uid2` = '$uid1'";
			$res = mysql_query($sql);
			while( $row = mysql_fetch_assoc($res) )
			{
				if( $row['uid1']==$uid1 )
					$simi[ $row['uid2'] ] = $row['simi'];
				else $simi[ $row['uid1'] ] = $row['simi'];
			}
			@mysql_free_result($res);
			arsort($simi);
			return $simi;
		}
		else
		{
			$this->uid_check_order($uid1, $uid2);
			$sql = "select `simi` from `similarity` where `uid1`='$uid1' and `uid2`='$uid2'";
			return floatval(tai_mysqlResult($sql));
		}
	}
}
global $simi;
$simi = new similarity_C;
/*
	echo intval($simi->set(1,3,0.123456789123456789123456789));
	echo $simi->get(5,1);
	echo '<pre>'.var_export($simi->get(1),true).'</pre>';
//*/
?>
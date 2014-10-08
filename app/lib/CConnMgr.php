<?php
class CConnMgr
{
	private $pdos = array();
	private $mongos = array();
	private $mems = array();
	private $rediss = array();
	
	private static $instance = null;
	
	private function __construct(){}
	
	public static function init()
	{
		if(CConnMgr::$instance == null)
		{
			CConnMgr::$instance = new CConnMgr();
		}
		return CConnMgr::$instance;
	}
	
	public function getPdo($host,$port,$db,$user,$pwd)
	{
		
		$key = $host.$port.$db;
		
		if($this->pdos[$key] == null)
		{
			$this->pdos[$key] = @new PDO("mysql:host=".$host.";port=".$port.";dbname=".$db,$user,$pwd,array(PDO::ATTR_TIMEOUT=>1000,PDO::ATTR_PERSISTENT => true, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
		}
		
		return $this->pdos[$key];
		
	}
		
	public function pdo(Array $info)
	{
		if($info ==false)
		{
			return null;
		}
		return $this->getPdo($info['host'],$info['port'],$info['db'],$info['user'],$info['pwd']);
	}	
	
	public function pdos(Array $info)
	{
		if($info ==false)
		{
			return null;
		}
		$arhosts = explode(',',$info['hosts']);
		$arports = explode(',', $info['ports']);
		$arusers = explode(',', $info['users']);
		$arpwds = explode(',', $info['pwds']);
		$ardbs = explode(',', $info['dbs']);
		$len = count($arhosts);
		
		$rpdos  = array();
		
		for($i=0;$i<$len;$i++)
		{
			$rpdos[] = $this->getPdo($arhosts[$i],$arports[$i],$ardbs[$i],$arusers[$i],$arpwds[$i]);
		}
		return $rpdos;
	}
	
	public function rpdos(Array $info)
	{
		if($info ==false)
		{
			return null;
		}
		$arhosts = explode(',', $info['rhosts']);
		$arports = explode(',', $info['rports']);
		$arusers = explode(',', $info['rusers']);
		$arpwds = explode(',', $info['rpwds']);
		$ardbs = explode(',', $info['rdbs']);
		$len = count($arhosts);
		
		$rpdos  = array();
		
		for($i=0;$i<$len;$i++)
		{
			$rpdos[] = $this->getPdo($arhosts[$i],$arports[$i],$ardbs[$i],$arusers[$i],$arpwds[$i]);
		}
		return $rpdos;
	}
	
	public function getMongo($host,$port,$db)
	{
		$key = $host.$port.$db;
		if($this->mongos[$key] == null)
		{
			$mongo =  new Mongo("mongodb://".$host.":".$port."/".$db,array('timeout'=>1000));
			$this->mongos[$key] = $mongo->selectDB($db);
		
		}
		return $this->mongos[$key];
	}
	
	public function mongo(Array $info)
	{
		
		if($info ==false)
		{
			return null;
		}
				
		return $this->getMongo($info['host'],$info['port'],$info['db']);
	}
	
	public function rmongos(Array $info)
	{
		if($info ==false)
		{
			return null;
		}
		$arhosts = explode(',', $info['rhosts']);
		$arports = explode(',', $info['rports']);
		$ardbs = explode(',', $info['rdbs']);
		$len = count($arhosts);
		
		$rmongos  = array();
		
		for($i=0;$i<$len;$i++)
		{
			$rmongos[] = $this->getMongo($arhosts[$i],$arports[$i],$ardbs[$i]);
		}
		return $rmongos;
	}
	
	public function mongos(Array $info)
	{
		if($info ==false)
		{
			return null;
		}
		$arhosts = explode(',', $info['hosts']);
		$arports = explode(',', $info['ports']);
		$ardbs = explode(',', $info['dbs']);
		$len = count($arhosts);
	
		$rmongos  = array();
	
		for($i=0;$i<$len;$i++)
		{
		$rmongos[] = $this->getMongo($arhosts[$i],$arports[$i],$ardbs[$i]);
		}
		return $rmongos;
	}
	
	public function getMem($hosts,$ports)
	{
		$key = $hosts.$ports;
		
		if($this->mems[$key] == null)
		{
			$memcache = new Memcache();
			$ahosts = explode(',',$hosts);
			$aports = explode(',',$ports);
		
			$len = count($ahosts);
		
			for($i=0;$i<$len;$i++)
			{
				$memcache->addServer($ahosts[$i],(int)$aports[$i],true);
			}
			$this->mems[$key] = $memcache;
		}
		
		return $this->mems[$key];
	}
	
	public function mem(Array $info)
	{
		if($info ==false)
		{
			return null;
		}
		return $this->getMem($info['hosts'], $info['ports']);
	}

	public function getRedis($host,$port)
	{
		$key = $host.$port;
		
		if($this->rediss[$key] == null)
		{
			$redis = new Redis();
			$redis->pconnect($host,$port,1);
			$this->rediss[$key] =$redis;
		}
		
		return $this->rediss[$key];
	}
	
	public function redis(Array $info)
	{
		if($info ==false)
		{
			return null;
		}
		
		return $this->getRedis($info['host'], $info['port']);

	}
	
	public function rediss(Array $info)
	{
		if($info ==false)
		{
			return null;
		}
		$arhosts = explode(',', $info['hosts']);
		$arports = explode(',', $info['ports']);

		$len = count($arhosts);
	
		$rediss  = array();
	
		for($i=0;$i<$len;$i++)
		{
			$rediss[] = $this->getRedis($arhosts[$i],$arports[$i]);
		}
		return $rediss;
	}
}

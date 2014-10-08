<?php
abstract class CCacheModel extends CModel
{
	
	abstract protected  function cache();
	
	protected  function prefix()
	{
		return strtolower(get_class($this));
	}
	
	protected function persist()
	{
		return false;
	}
	
	protected function source()
	{
		return null;
	}
	
	protected function delayWrite()
	{
		return false;
	}
	
	protected function modifyList()
	{
		return null;
	}
	
	public  function getNKey()
	{
		$keyName = $this->keyName();
	
		if($this->$keyName == null || $this->$keyName == '' || $this->$keyName == 0)
			throw new ErrorException('the primay key field '.$keyName.' in '.get_class($this).' not setted');
	
		if($this->prefix() != '')
			return $this->prefix()."_".$this->$keyName;
		else
			return strtolower(get_class($this))."_".$this->$keyName;
	}
			
	public function save()
	{
		$keyName=$this->keyName();
		$nskey = $this->getNKey();
		$val = $this->toArray();
		
		if($this->persist())
		{
			if($this->delayWrite()) //缓写支持
			{
				if($this->modifyList()) 
				{ 
					$this->modifyList()->push($this->$keyName);
				}
				else
				{
					throw new CModelException('modifyList is null in '.get_class($this));
				}
			}
			else
			{
				if($this->source())	
				{
					if($this->isCreate())
					{
						$this->source()->add($this->$keyName,$val);
					}
					else 
					{
						$this->source()->set($this->$keyName,$val);
					}
				}
				else
				{
					throw new CModelException('source is null in '.get_class($this));
				}
			}

		}

		$ret = false;
		if($this->isCreate())
		{
			$ret = $this->cache()->add($nskey,CUtils::encode($val));
			if(!$ret)
			{
				throw new CModelException($nskey.' add to cache fail in '.get_class($this));
			}
		}
		else
		{
			$ret = $this->cache()->set($nskey,CUtils::encode($val));
			if(!$ret)
			{
				throw new CModelException($nskey.' save to cache fail in '.get_class($this));
			}
		}
	}

	public function get($key)
	{
		$this->setKey($key);
		$var = $this->cache()->get($this->getNKey());

		if($var !== false)
		{
			$this->fromArray(CUtils::decode($var))->setDirty(false);
			return $this;
		}
		else
		{
			if($this->source()) 
			{
				$var = $this->source()->get($key);
				if($var !== false)
				{
					$this->fromArray($var)->setDirty(false);
					$this->cache()->set($this->getNKey(),CUtils::encode($var));
					return $this;
				}
				$this->setDirty(false);
				return false;
			}
			$this->setDirty(false);
			return false;
		}
	}
	
	public static function mget($keys)
	{
		
		$caller= get_called_class();
		$callerObj= new $caller();
		
		$objs = array();
		$nsKeys = array();
		for($i=0; $i < count($keys); $i++)
		{
			$obj=new $caller();
			$obj->setKey($keys[$i]);
			$objs[$keys[$i]] = $obj;
			$nsKeys[$obj->getNKey()]=$keys[$i];
		}
		
		$vars = $callerObj->cache()->get(array_keys($nsKeys));

		if($vars !== false && count($vars) > 0 )
		{
			
			foreach ($vars as $key =>$var)
			{
				$objs[$nsKeys[$key]]->fromArray(CUtils::decode($var,true))->setDirty(false);
				
				unset($nsKeys[$key]);
			}

			if( count($vars) < count($keys) ) //内存中只有部分KEY时，从源里面取
			{

				if($callerObj->source()) //只有当源存在时才取
				{
					$vars = $callerObj->source()->get(array_values($nsKeys));
					if( $vars !== false)
					{
					
						foreach ($vars as $key =>$var)
						{
							$objs[$key]->fromArray($var)->setDirty(false);
							$callerObj->cache()->set($objs[$key]->getNKey(),json_encode($var));//写回memcached
							unset($nsKeys[$objs[$key]->getNKey()]);
						}
					}
				}

				foreach($nsKeys as $key => $value)
				{
					unset($objs[$value]);
				}
				return $objs;
			}
			else 
			{
				return $objs;
			}
		}
		else //取失败时从源里面取
		{
			if($callerObj->source())
			{
				$vars = $callerObj->source()->get($keys);
				if( $vars !== false)
				{
					foreach ($vars as $key =>$var)
					{
						$objs[$key]->fromArray($var);
						$callerObj->cache()->set($objs[$key]->getNKey(),CUtils::encode($var));//写回memcached
					}
					$diffKeys=array_diff($keys,array_keys($vars));
					foreach($diffKeys as $key)
					{
						unset($objs[$key]);
					}
					return $objs;
				}
			}
			return false;
		}
	}

	public function delete($key)
	{
		$this->setKey($key);
		
		if($this->cache()->delete($this->getNKey()))
		{
			if($this->persist())
			{
				if($this->source())	return $this->source()->delete($key);
			}
			return true;
		}
		return false;
	}

}


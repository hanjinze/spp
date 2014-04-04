<?php
abstract class CModel
{
	private $dirty = false;
	private $keyName = null;
	
	protected  function fields()
	{
		$caller = get_called_class();
		return $caller::$fields;
	}
	
	public function isDirty()
	{
		return $this->dirty;
	}
	
	public function setDirty($flag)
	{
		$this->dirty = $flag;
		return $this;
	}
	
	protected  function keyName()
	{
		if(!isset($this->keyName) || $this->keyName == null)
		{
			$keys= array_keys($this->fields());
			$this->keyName = $keys[0];
		}
		return $this->keyName;
	}
	
	public function setKey($key)
	{
		$keyName = $this->keyName();
		$this->$keyName = $key;
		$this->dirty = true;
		return $this;
	}
	
	public function getKey()
	{
		$keyName = $this->keyName();
		return $this->$keyName;
	}
	
	public function fromArray($infos = null)
	{
		if( is_array($infos) || is_object($infos))
		{
			$fields = $this->fields();
			foreach($infos as $key => $var)
			{
				if(array_key_exists($key, $fields))
				{
					$this->$key = $var;
				}
			}
		}
		return $this;
	}

	public function toArray()
	{
		$infos=array();
		$fields = $this->fields();
		$keyName = $this->keyName();
		
		if($this->$keyName == null || $this->$keyName == '' )
		{
			throw new CModelException('primay key field '.$this->$keyName.' is not set in '.get_class($this));
		}
		
		foreach($fields as $key => $var)
		{
			$infos[$key] = $this->$key;
		}
		return $infos;
	}

	public function __call($m,$a)
	{
		$do =substr($m,0,3);
		if($do =='get')
		{
			$field = substr($m,3);
			$field[0]=strtolower($field[0]);
			return $this->$field;
		}
		else if ($do == 'set')
		{
			$field = substr($m,3);
			$field[0]=strtolower($field[0]);
			if($this->$field !==  $a[0])
			{
				$this->$field = $a[0];
				$this->dirty = true;
			}
			return $this;
		}
		else
		{
			throw new CModelException('can not find method '.$m.' in '.get_class($this));
		}
	}
	

	public function __get($field)
	{
		if(array_key_exists($field,$this->fields()))
		{
			//print_r($this->$field);
			
			if(!isset($this->$field) || $this->$field === null)
			{
				$fields = $this->fields();
				$this->$field = $fields[$field];
			}
			return $this->$field;
		}
		else
		{
			throw new CModelException('can not find field '.$field.' in '.get_class($this));
		}

	}

	public function __set($field, $value)
	{
		if(array_key_exists($field,$this->fields()))
		{
			if(!isset($this->$field) || $this->$field !== $value)
			{
				$this->$field = $value;
				$this->dirty = true;
			}
		}
		else
		{
			throw new CModelException('can not find field '.$field.' in '.get_class($this));
		}
		return $this;

	}

	
	public static function model()
	{
		$caller = get_called_class();
		return new $caller();
	}
	
	abstract public  function save();

	abstract public  function get($key);
	
	abstract public  function delete($key);
	
}




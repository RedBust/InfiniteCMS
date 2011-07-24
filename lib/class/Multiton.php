<?php

/**
 * implements the Multiton design pattern
 *
 * @file $Id: Multiton.php 20 2010-09-24 08:51:22Z nami.d0c.0 $
 */
class Multiton
{
	protected $infos = array(),
	$name = NULL;
	protected static $instances = array();

	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	public function setOptions($options)
	{
		$this->infos = $options;
		return $this;
	}

	protected function __construct(array $options, $name)
	{
		$this->setName($name)
				->setOptions($options);
	}

	public function __destruct()
	{
		$this->destruct();
	}

	/**
	 * @override
	 */
	protected function init()
	{
		
	}

	/**
	 * @override
	 */
	protected function destruct()
	{
		
	}

	protected function get($name)
	{
		if ($this->has($name))
			return $this->infos[$name];
		return null;
	}

	protected function set($name, $value)
	{
		$this->infos[$name] = $value;
	}

	protected function has($name)
	{
		return isset($this->infos[$name]);
	}

	protected function remove($name)
	{
		if (!$this->has($name))
			return false;
		unset($this->infos[$name]);
	}

	public function __get($name)
	{
		return $this->get($name);
	}

	public function __set($name, $value)
	{
		return $this->set($name, $value);
	}

	public function __isset($name)
	{
		return $this->has($name);
	}

	public function __unset($name)
	{
		return $this->delete($name);
	}

	protected function offsetGet($name)
	{
		return $this->get($name);
	}

	public function offsetSet($name, $value)
	{
		return $this->set($name, $value);
	}

	public function ossetIsset($name)
	{
		return $this->has($name);
	}

	public function offsetUnset($name)
	{
		return $this->delete($name);
	}

	public function __call($method, $args)
	{
		$type3 = substr($method, 0, 3);
		$type5 = substr($method, 0, 5);
		if ($is3 = ( $type3 == 'get' || $type3 == 'set' )
				|| $type5 == 'isset' || $type5 == 'unset')
		{
			$col = array(lcfirst(substr($method, $is3 ? 3 : 5 )));
			return call_user_func_array(array($this, $is3 ? $type3 : $type5), array_merge($col, $args));
		}
	}

	public static function getInstance($name = '_', $params = array())
	{
		$class = get_called_class();
		if (!isset(self::$instances[$class][$name]))
		{
			self::$instances[$class][$name] = new $class($params, $name);
			self::$instances[$class][$name]->init();
		}
		return self::$instances[$class][$name];
	}
}
<?php

/**
 * manages exceptions to be thrown
 *
 * @file $Id: ExceptionManager.php 20 2010-09-24 08:51:22Z nami.d0c.0 $
 *
 * @extends Exception
 */
class ExceptionManager extends Exception
{
	/**
	 * Don't use this method anymore !
	 *
	 * @param string $method method who throw this Exception
	 * @param string $instead method to use instead
	 */
	static public function deprecatedMethod($method, $instead = NULL)
	{
		$msg = sprintf('The method %s is deprecated', $method);
		if ($instead !== NULL)
		{
			$msg .= sprintf(', use %s instead !', self::_getClassFor($instead, $method));
		}
		throw new self($msg . ' !');
	}

	/**
	 * You missed a check !
	 *
	 * @param string $context the waited context
	 */
	static public function wrongContext($context)
	{
		$backtrace = debug_backtrace();
		throw new self(sprintf('You call the method %s with a wrong context (waiting context : %s)', self::_getLastMethod(), $context));
	}

	/**
	 * What are you trying to nest ?
	 */
	static public function nestingCache()
	{
		throw new self('You\'re trying trying to nest cache !');
	}

	/**
	 * THAT'S NOT A DIR
	 */
	static public function invalidDir($dir)
	{
		throw new self(sprintf('The %s dir is not a valid dir.', $dir));
	}

	/**
	 * WHY CAN'T I :(((((( !
	 */
	static public function cantCreate($file)
	{
		throw new self(sprintf('Unable to create %s', $file));
	}

	/**
	 * returns the class+method if given for the given param
	 *
	 * @param string $class
	 * @param string $method
	 * @return string
	 *
	 * @access protected
	 */
	protected static function _getClassFor($class, $method)
	{
		if ($class[0] == '!')
		{
			$class = substr($class, 1); //don't add class
		} elseif (strpos('::', $class) === false) //no class
		{
			$class = explode('::', $method);
			$class = $class[0] . '::' . $class;
		}
		return $class;
	}

	/**
	 * return the last called method, with file+line
	 *
	 * @param $backtrace_index Where to start the backtrace ? (default : 1, with 0 the method calling this method'd be returned)
	 * 
	 * @access protected
	 */
	protected static function _getLastMethodCalled($backtrace_index = 1)
	{
		$backtrace = debug_backtrace();
		return sprintf('%s on %s:%d', $backtrace[$backtrace_index]['function'], $backtrace[$backtrace_index]['file'], $backtrace[$backtrace_index]['line']);
	}
}
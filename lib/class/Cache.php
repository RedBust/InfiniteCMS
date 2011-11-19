<?php

/**
 * Implements cache management
 *
 * @file $Id$
 */
class Cache
{
	const DIR = 'cache';

	const SKIP = false,
		SHOW = true,

		JS = true,
		NO_JS = false,

		CARET = true,
		END = false,

		ACT_LOAD = 0,
		ACT_NOTHING = 1;

	static protected $cachedFiles = null,
		$actual = null,
		$replacements = array(),
		$dirFormat = '%dir%',
		/* @var string Var prefix. NOTE : if not empty, followed by an underscore ! (ie with varPrefix = '%%cache%%' and varName = 'test', will result in '%%cache%%_test' */
		$varPrefix = '';
	protected $name = null,
		$vars = array(),
		$input = '';

	/**
	 * sets dir format
	 * The dir MUST exist.
	 *
	 * @param string $format path format (dir)
	 */
	static public function setDirFormat($format)
	{
		self::$dirFormat = $format;
	}

	/**
	 * returns dir format
	 * 
	 * @return string
	 */
	static public function getDirFormat()
	{
		return self::$dirFormat;
	}
	static public function ensureDir($add = '')
	{
		$dir = self::getDir() . ltrim($add, '/');
		if (file_exists($dir))
		{
			if (!is_dir($dir))
				throw ExceptionManager::invalidDir('cache');
		}
		else
		{
			$dir = explode('/', str_replace(DIRECTORY_SEPARATOR, '/', $dir));
			$cDir = ''; //complete dir;
			foreach ($dir as $d)
			{
				$cDir .= $d . '/';
				if (!file_exists($cDir) && !@mkdir($cDir))
					throw ExceptionManager::cantCreate('cache dir (' . $cDir . ')');
			}
		}
	}
	static public function getDir()
	{
		return trim(self::_format(self::$dirFormat, array('%dir%' => self::DIR)), '/') . '/';
	}

	/**
	 * formats a string
	 *
	 * @param type $str the string to format
	 * @param type $replacements the remplacements
	 * @param type $includes add the global replacements ?
	 * 
	 * @return string
	 */
	protected static function _format($str, $replacements, $includes = true)
	{
		if ($includes)
			$replacements = array_merge(self::$replacements, $replacements);

		return strtr($str, $replacements);
	}
	static public function addReplacement($name, $value)
	{
		self::$replacements[$name] = $value;
	}

	/**
	 * sets var prefix. 
	 *
	 * @param boolean $activate whether it must be enabled or not
	 */
	static public function setVarPrefix($prefix)
	{
		self::$varPrefix = $prefix;
	}

	/**
	 * gets var prefix
	 */
	static public function getVarPrefix($prefix)
	{
		return self::$varPrefix;
	}

	static protected function _load()
	{
		if (self::$cachedFiles === null)
		{
			self::$cachedFiles = array();
			self::_loadFiles(self::getDir());
		}
	}
	static protected function _loadFiles($dir)
	{
		$files = glob(( $dir = rtrim($dir, '/') . '/' ) . '*');
		$dirLen = strlen(self::getDir()); //it's getDir() and not $dir cause we wanna keep the "real" dir
		foreach ($files as $file)
		{
			if (is_dir($file))
				self::_loadFiles($file);
			else
				self::$cachedFiles[substr($file, $dirLen)] = filemtime($file); //strip cache/ and .php
		}
	}
	

	/**
	 * starts caching ... Or require cached file if present & valid.
	 *
	 * @param int|string $lifeTime cachefile's lifetime (integer or strotime() arg). -1 for infinite lifetime
	 * @return Cache|false Cache instance if the cache needs to be refreshed, false if the cache is still valid
	 */
	public static function start($name, $lifeTime = -1, $act = NULL)
	{
		if (self::$actual != NULL)
			throw ExceptionManager::nestingCache(self::$actual, $name);
		if ($act == NULL)
			$act = self::ACT_LOAD;

		self::_load();
		$name = self::getNameFor($name);
		if (isset(self::$cachedFiles[$name])
				&& ($lifeTime == -1 || !date_passed(self::$cachedFiles[$name], $lifeTime)))
		{
			if ($act == self::ACT_LOAD)
			{
				require self::getDir() . $name;
				$prefix = self::_formatPrefix($name);
				if (isset($vars))
				{
					foreach ($vars as $vname)
					{
						$GLOBALS[$prefix . $vname] = $$vname;
					}
				}
			}
			return false;
		}
		else
		{
			self::destroy($name); //remove the cache as it's not used anymore.
			return new self($name);
		}
	}

	/**
	 * destroys a cache file
	 *
	 * @param string $name cache's name
	 * @return void
	 */
	public static function destroy($name)
	{
		self::_load();
		if (isset(self::$cachedFiles[$name]))
		{ //since this will be called even everytime a cache is generated
			@unlink(self::getDir() . $name . EXT);
			unset(self::$cachedFiles[$name]);
		}
	}

	/**
	 * destroys some cached files by a prefix (auto _ appended)
	 *
	 * @param string $prefix
	 * @todo return number of deleted occurences ?
	 */
	public static function destroyPrefix($prefix)
	{
		self::_load();
		foreach (self::$cachedFiles as $name => $lifeTime)
		{
			if (strpos($name, $prefix . '_') === 0)
			{
				@unlink(self::getDir() . $name . EXT);
				unset(self::$cachedFiles[$name]);
			}
		}
	}

	/**
	 * destroys some cached files by a regexp (nothing prepended / appended)
	 *
	 * @param string $regexp the regexp
	 * @todo return number of deleted occurences ?
	 */
	public static function destroyRegexp($regexp)
	{
		self::_load();
		foreach (self::$cachedFiles as $name => $lifeTime)
		{
			if (preg_match($regexp, $name))
			{
				@unlink(self::getDir() . $name . EXT);
				unset(self::$cachedFiles[$name]);
			}
		}
	}

	/**
	 * exports variable in eval'able PHP format
	 *
	 * @param boolean $phpTags=true add PHP tags ?
	 */
	protected function _exportVars($phpTags = true)
	{
		if (empty($this->vars))
			return '';

		$code = '';

		if ($phpTags)
			$code .= '<?php ';
		$vars = array_merge($this->vars, array('vars' => array_keys($this->vars)));

		foreach ($vars as $name => $value)
		{
			$code .= sprintf('$%s = %s; ', $name, var_export($value, true));
		}

		return $code . ( $phpTags ? ' ?>' : '' );
	}

	/**
	 * exports javascript code in jQ() call format.
	 */
	protected static function _exportJS($phpTags = true)
	{
		$js = jQ(NULL, 'cache');
		jQ($js, 'cache'); //pretty stupid, yeah.
		if (!empty($js))
			return ($phpTags ? '<?php ' : '') . 'jQ(' . var_export($js, true) . '); ' . ($phpTags ? ' ?>' : '');
	}

	/**
	 * returns formatted prefix + an underscore if not empty
	 *
	 * @param string $name cache's name
	 */
	static protected function _formatPrefix($name)
	{
		if (empty(self::$varPrefix))
			return '';
		else
			return self::_format(self::$varPrefix, array('cache' => $name)) . '_';
	}

	static protected function getNameFor($name)
	{
		return strpos($name, '.') === false ? $name . EXT : $name;
	}
	public function getName()
	{
		return self::getNameFor($this->name);
	}

	/**
	 * saves cache to it's file + print it on screen. Also, it destructs the class.
	 *
	 * @param boolean $show show the content just got cached ?
	 * @param boolean $js include JS ?
	 */
	public function save($show = null, $js = null)
	{
		if ($show === null)
			$show = self::SHOW;
		if ($js === null)
			$js = self::JS;

		$content = $this->_exportVars() . ($js == self::JS ? $this->_exportJS() : '') . ob_get_contents() . ' ' . $this->input;
		file_put_contents(self::getDir() . $this->getName(), $content);
		if ($show == self::SHOW)
		{ //if you want to nest a cache inside another WITH variables (i.e.) ...
			ob_flush();
		}
		$prefix = self::_formatPrefix($this->getName());
		foreach ($this->vars as $vname => $value)
		{
			$GLOBALS[$prefix . $vname] = $value;
		}
		ob_end_clean();
		unset($this);
	}

	public function clear()
	{
		ob_end_clean();
	}

	/**
	 * adds string BUT does not show that
	 */
	public function put($str, $at = null)
	{
		if ($at == null)
			self::END;

		if ($at == self::CARET)
		{
			$this->input .= ob_end_clean() . $str;
			ob_start();
		}
		else
			$this->input .= $str;
	}

	/**
	 * sets a variable
	 *
	 * @param string $name variable's name
	 * @param mixed $value variable's value
	 */
	public function set($name, $value)
	{
		$this->vars[$name] = $value;
	}

	protected function __construct($name)
	{
		$this->name = $name;
		ob_start(); //php allows nested ouput buffering
	}

	public function __destruct()
	{
		self::$actual = null;
	}
}
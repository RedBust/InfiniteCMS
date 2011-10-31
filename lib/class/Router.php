<?php

define('ROOT_ACTIONS', ROOT . 'actions/');

/**
 * makes all the route business and all logic
 *
 * @file $Id: Router.php 53 2011-01-15 11:11:37Z nami.d0c.0 $
 */
class Router extends Multiton
{
	const GET = 'GET',
		POST = 'POST';

	protected $def_controller = 'Index',
	$def_action = 'index',
	$def_ext = EXT,
	$replaces = array(
		'ext' => array(
			'/' => EXT, //fallback value
		),
	),
	$_controller = false,
	$_action = false,
	$_ext = false,
	$_use = array(),
	$_rules = array(),
	$_alias = null,
	$_stockR = null,
	$_isRoute = false;

	protected function _xmlAsString($value)
	{
		if (is_object($value) && method_exists($value, 'asXML') && !method_exists($value, '__toString'))
			return $value->asXML();
		else
			return (string) $value;
	}

	protected function init()
	{
		global $config;
		if ($this->_stockR === null)
			$this->_stockR = $_REQUEST; //we stock a copy
		try
		{
			/** Personnal note:
			 * If you want to customize the CMS, and if your skills are not good as Rasmus Ledorf's,
			 *  I recommend for you to skip this module and to go in any else file.
			 * SimpleXMElement is the biggest SHIT I've ever seen in my entire dev life
			 * between bugs, bugged comportement and unnexistant/undocumented functions ... You can't do anything
			 *  without loosing at least 1h. Believe me, you should go further.
			 */
			$file = new SimpleXMLElement('models/rewrite.xml', null, true); //open a file
		} catch (Exception $e)
		{
			exit(sprintf('Err(o|eu)r: %s', $e->getMessage()));
		}

		$alias = array();
		foreach ($file->rewrite[0] as $k => $types)
		{ //foreach all rewrite rules
			foreach ($types as $t => $type)
			{
				if (!isset($this->_use[$t]))
					$this->_use[$t] = $alias[$t . 's'] = array(); //new type

				$attributes = $type->attributes();
				$name = trim(str_replace(array('name=', '"'), '', $this->_xmlAsString($attributes['name'])));
				foreach ($type as $n => $value)
				{ //all aliases for this type
					$attrs = $value->attributes();
					$value = strip_tags($this->_xmlAsString($value));
					if (isset($attrs['use']) && intval(trim(str_replace(array('use=', '"'), '', $this->_xmlAsString($value['use'])))))
						$this->_use[$t][$name] = $value; //default?

					$alias[$t . 's'][$value] = $name;
				}
			}
		}

		$this->_alias = $alias; //stock alias ary
		foreach ($file->routes[0] as $route)
		{ //foreach all routes
			$find = $route->find[0];
			$replace = $route->replace[0];
			$a = $e = $c = NULL;
			$c = $this->getControllerAlias($this->_xmlAsString($find->controller));
			if (isset($alias['controllers'][$c]))
				$c = $alias['controllers'][$c];
			$f = $c . '|';

			if (isset($find->action))
			{
				$a = $this->getActionAlias($this->_xmlAsString($find->action));
				$f .= $a . '|';
				if (isset($find->ext)) //can only spec. a ext if a action is spec.
				{
					$e = $this->getExtAlias($this->_xmlAsString($find->ext));
					$f .= $e;
				}
				else
					$f .= '-1';
			}
			else
				$f .= '-1|-1';

			//default or submitted ?
			$r['controller'] = $c === NULL ? $this->getControlerUse($this->def_controller) : $e;
			$r['action'] = $a === NULL ? $this->getActionUse($this->def_action) : $a;
			$r['ext'] = $e === NULL ? $this->getExtUse($this->def_ext) : $e;
			foreach ($replace as $k => $v)
				$r[$k] = $this->_xmlAsString($v);

			$this->_rules[$f] = $r;
		}

		if ($config['REWRITE'] && isset($_GET['rewrite']) && $_GET['rewrite'])
		{
			//create the URL from the request
			$dir = pathinfo($_SERVER['SCRIPT_NAME']);
			if ($dir['dirname'] == '\\')
				$dir['dirname'] = '';
			$dir = $dir['dirname'] . DS; //remove the file name
			$real_uri = explode('?', substr($_SERVER['REQUEST_URI'], strlen($dir)));
			$real_uri = $real_uri[0];
			$uri = explode('.', $real_uri);
			if (isset($uri[1])) //if we have an ext
				$_REQUEST['ext'] = $uri[1];
			$params = explode('?', $uri[0]);
			$params = $params[0];
			$params = explode('/', trim($params, '/'));
			$key = NULL;
			$hasId = false;
			$paramsCount = count($params);
			foreach ($params as $i => $part)
			{
				if ($part === '')
					continue;

				if ($i === 0) //first
					$_REQUEST['controller'] = $part;
				else if ($i === 1) //second
					$_REQUEST['action'] = $part;
				else
				{
					if ($i === 2 && is_numeric($part) && $paramsCount < 5)
					{ //controller, action, id[, mode]
						$_REQUEST['id'] = $part;
					} else if (isset($_REQUEST['id']) && $i === 3 && $paramsCount == 4)
						$_REQUEST['mode'] = $part;
					else if ($key === NULL)
						$key = $part; //futur key
					else
					{ //pair key=value
						$_REQUEST[$key] = $part;
						$key = NULL;
					}
				}
			}
		}

		//check IsRoute?
		$total = sprintf('%s|%s|%s', $c_ = $this->_getBase('controller'), $a_ = $this->_getBase('action'), $e_ = $this->_getBase('ext'));
		$isRule = array(
			'*' => isset($this->_rules[$total]),
			'c' => isset($this->_rules[$c_ . '|-1|-1']),
			'a' => isset($this->_rules[$c_ . '|' . $a . '|-1']),
			'e' => isset($this->_rules[$c_ . '|-1|' . $e]),
		);

		if ($isRule['*'] || $isRule['c'] || $isRule['a'] || $isRule['e'])
		{
			//get the rule type :p
			$r = $isRule['*'] ? $this->_rules[$total] :
					( $isRule['c'] ? $this->_rules[$c_ . '|-1|-1'] :
							( $isRule['a'] ? $this->_rules[$c_ . '|' . $a . '|-1'] :
									$this->_rules[$c_ . '|-1|' . $e] ) );
			if (isset($r['controller']))
			{
				$c = $this->getControllerAlias($r['controller']);
				$_REQUEST['controller'] = $c;
				$this->_controller = $c;
			}
			if (isset($r['action']))
			{
				$a = $this->getActionAlias($r['action']);
				$_REQUEST['action'] = $a;
				$this->_action = $a;
			}
			if (isset($r['ext']))
			{
				$e = $this->getExtAlias($r['ext']);
				$_REQUEST['ext'] = $e;
				$this->_ext = $e;
			}
			$this->_isRoute = true;
			global $title;
			$title = lang($c . ' - ' . $a, 'title');
			foreach ($r as $k => $v)
			{ //parse all {} in rewrite
				if (strpos($v, '{') !== false)
				{
					if (preg_match_all('`{([a-zA-Z]+)}`', $v, $match))
					{
						foreach ($match[1] as $var)
							if (isset($this->_stockR[$var]))
								$v = str_replace('{' . $var . '}', $this->_stockR[$var], $v);
					}
				}
				$_REQUEST[$k] = $v;
			}
		}
		$this->_addType('controller', ROOT_ACTIONS . '%s' . DS);
		$this->_addType('action', ROOT_ACTIONS . $this->getController() . DS . '%s' . EXT);
		$path = ROOT_ACTIONS . $this->getController() . DS . $this->getAction() . '%s';
		$this->_addType('ext', $path, 'getExtFor');

		if (substr($this->getAction(), 0, 1) == '_')
			$this->_action = null;
	}

	/**
	 * isAjax
	 *
	 * @return boolean Request from AJaX ?
	 */
	public function isAjax()
	{
		return ( isset($_SERVER['HTTP_X_REQUESTED_WITH'])
		&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' );
	}

	public function isRoute()
	{
		return $this->_isRoute;
	}

	public function rewrite()
	{
		global $config;
		return $config['REWRITE'];
	}

	/**
	 * postVar
	 * return a key from _POST
	 *
	 * @param string $n name of the key
	 * @param mixed $d Default value
	 * @return mixed value of the key or $d
	 *
	 * @static
	 * @access public
	 */
	public function postVar($n, $d=NULL)
	{
		return isset($_POST[$n]) ? $_POST[$n] : $d;
	}

	/**
	 * postVars
	 * return an array with keys from _POST
	 *
	 * @param string [func_get_args] keys
	 * @return array Associative array.
	 */
	public function postVars()
	{
		$args = func_get_args();
		$values = array();
		foreach ($args as $arg)
		{
			$values[$arg] = $this->postVar($arg);
		}
		return $values;
	}

	/**
	 * getVar
	 * return a key from _GET
	 *
	 * @param string $n name of the key
	 * @param mixed $d Default value
	 * @return mixed value of the key or $d
	 *
	 * @access public
	 */
	public function getVar($n, $d=NULL)
	{
		return isset($_GET[$n]) ? $_GET[$n] : $d;
	}

	/**
	 * getVars
	 * return an array with keys from _GET
	 *
	 * @param string [func_get_args] keys
	 * @return array Associative array.
	 */
	public function getVars()
	{
		$args = func_get_args();
		$values = array();
		foreach ($args as $arg)
		{
			$values[$arg] = $this->getVar($arg);
		}
		return $values;
	}

	/**
	 * requestVar
	 * return a key from _REQUEST
	 *
	 * @param string $n name of the key
	 * @param mixed $d Default value
	 * @return mixed value of the key or $d
	 *
	 * @access public
	 */
	public function requestVar($n, $d=NULL)
	{
		return isset($_REQUEST[$n]) ? $_REQUEST[$n] : $d;
	}

	public function requestVars()
	{
		$args = func_get_args();
		$values = array();
		foreach ($args as $arg)
		{
			$values[$arg] = $this->requestVar($arg);
		}
		return $values;
	}

	//little bit useless
	public function getDefault($for)
	{
		return $this->{'def_' . $for};
	}

	protected function _getBase($type)
	{
		$c = $this->requestVar($type, $this->{'def_' . $type});
		if (empty($c))
			return $this->{'def_' . $type};
		if (isset($this->replaces[$type][$c]))
			return $this->replaces[$type][$c];
		return $c;
	}

	protected function _addType($type, $path, $formateMethod = NULL)
	{
		$act = $this->_getBase($type);
		if (empty($act))
			$act = $this->{'def_' . $type};
		$_act = empty($formateMethod) ? $act : $this->$formateMethod($act);
		if (file_exists(sprintf($path, $_act)))
			$act = str_replace('/', '', $_act);
		else
		{
			if (isset($this->_alias[$type . 's'][$act]))
			{
				$act = $this->_alias[$type . 's'][$act];
				$act = empty($formateMethod) ? $act :
						call_user_func(array(__CLASS__, $formateMethod), $act);
				if (!file_exists(sprintf($path, $act)))
					$act = NULL;
			}
			else
				$act = NULL;
		}
		$this->{'_' . $type} = $act;
	}

	public function getController()
	{
		return $this->_controller;
	}

	public function getAction()
	{
		return $this->_action;
	}

	public function getExt()
	{
		return $this->_ext;
	}

	public function getControllerUse($c)
	{
		if (isset($this->_use['controller'][$c]))
			return $this->_use['controller'][$c];
		return $c;
	}

	public function getActionUse($a)
	{
		if (isset($this->_use['action'][$a]))
			return $this->_use['action'][$a];
		return $a;
	}

	public function getExtUse($e)
	{
		if (isset($this->_use['ext'][$e]))
			return $this->_use['ext'][$e];
		return $e;
	}

	public function getControllerAlias($c)
	{
		if (isset($this->_alias['controllers'][$c]))
			return $this->_alias['controllers'][$c];
		return $c;
	}

	public function getActionAlias($a)
	{
		if (isset($this->_alias['actions'][$a]))
			return $this->_alias['actions'][$a];
		return $a;
	}

	public function getExtAlias($e)
	{
		if (isset($this->_alias['exts'][$e]))
			return $this->_alias['exts'][$e];
		return $e;
	}

	public function formateExt($ext)
	{
		if (isset($ext[0]) && $ext[0] === '.')
			return substr($ext, 1);
		else
			return $ext;
	}

	public function getExtFor($ext = NULL)
	{
		if ($ext === NULL)
			return $this->getExt();

		$base = $this->formateExt(EXT);
		$ext = $this->formateExt($ext);
		return $ext === $base ? EXT : '.' . $ext . EXT;
	}

	public function getInfos()
	{
		return array(
			'controller' => $this->getController(),
			'action' => $this->getAction(),
			'ext' => $this->getExt(),
		);
	}

	public function getPath($c = NULL, $a = NULL, $e = NULL)
	{
		$c = $c === NULL ? $this->getController() : $c;
		$a = $a === NULL ? $this->getAction() : $a;
		$e = $this->getExtFor($e);
		return ROOT_ACTIONS . $c . DS . $a . $e;
	}


	public function isMethod($method)
	{
		return $_SERVER['REQUEST_METHOD'] == $method;
	}
	public function isPost()
	{
		return $this->isMethod(self::POST);
	}
	public function isGet()
	{
		return $this->isMethod(self::GET);
	}

	public function codeIf($code, $if)
	{
		if ($if)
			throw new Router_CodeException($code);
	}
	public function codeUnless($code, $unless)
	{
		return $this->codeIf($code, !$unless);
	}
}

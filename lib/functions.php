<?php
defined('ROOT') || exit;
if (DEBUG && !DEV)
	$mem .= memory_get_usage() . ': debut fonctions<br />';

/**
 * dumps a variable then stop
 *
 * @package Debug
 */
function vdump()
{
	$args = func_get_args();
	call_user_func_array('vardump', $args);
	exit;
}

/**
 * like var_dump but with "pre" formatting
 *
 * @package Debug
 *
 * @param mixed $v Variable to dump
 * @return void
 */
function vardump()
{
#	debug_print_backtrace();
	$args = func_get_args();
	//move <pre> here ?
	foreach ($args as $arg)
	{
		echo '<pre>';
		var_dump($arg);
		echo '</pre>';
	}
	//move </pre> here ?
}

/**
 * like print_r but with <pre> formatting
 *
 * @subpackage Debug
 *
 * @param array $a Array to dump
 * @param boolean $s -default:NULL Stop?
 *
 * @return void
 */
function printr($a, $s = false)
{
	if (DEBUG)
	{
		tag('pre', print_r($a, true));
		stop($s);
	}
	else
	{
		echo '<!-- Debuggage encore actif - %s-->';
		debug_print_backtrace(); //trace
	}
}

if (!function_exists('ucfirst'))
{
	/**
	 * ucfirst
	 *
	 * @see ucfirst
	 * @ignore
	 */
	function ucfirst($str)
	{
		$str[0] = strtolower($str[0]);
		return $str;
	}

}
if (!function_exists('lcfirst'))
{
	/**
	 * lcfirst
	 *
	 * @see lcfirst
	 * @ignore
	 */
	function lcfirst($str)
	{
		$str[0] = strtolower($str[0]);
		return $str;
	}

}

/**
 * includes a lib'
 *
 * @subpackage Load
 *
 * @param string $t Name of the class
 *
 * @return content returned by the file
 */
function load($t)
{
	static $modified = false;
	if (!$modified)
	{
		define('EX_INCLUDE_PATH', get_include_path());
		set_include_path(get_include_path() . PATH_SEPARATOR . 'lib/class/');
		$modified = true;
	}
	$t = str_replace(array('\\', '_',), DS, $t) . EXT;
	return require $t;
}

spl_autoload_register('load');

/**
 * determinates if a user is authorized or not (>=)
 *
 * @package Helper
 * @subpackage Auth
 *
 * @param integer $required -default=LEVEL_LOGGED Lvl needed
 * @param boolean $reverse Lower than ?
 *
 * @return boolean authorized?
 */
function level($required = LEVEL_LOGGED, $reverse = false)
{
	global $member;
	if ($required == LEVEL_GUEST)
		return $reverse ? $member->isConnected() : !$member->isConnected(); //return true if !connected (without reverse)
	if ($required == LEVEL_LOGGED)
		return $reverse ? !$member->isConnected() : $member->isConnected(); //return true if connected (without reverse)

	global $account;
	//in case of banned, $lvl is LEVEL_BANNED
	$lvl = $member->isConnected() ? ( $account->banned ? LEVEL_BANNED : $account->level ) : LEVEL_GUEST;
	return ( $reverse ) ? $lvl < $required : $lvl >= $required;
}

/**
 * checks the level of a member for accessing to a page
 *
 * @package Helper
 * @subpackage Auth
 *
 * @param integer $lvl required level
 * @param boolean $lt Lower than ? (greater than(false) by default)
 *
 * @return boolean level's result
 */
function check_level($lvl = LEVEL_ADMIN, $lt = false)
{
	$check = level($lvl, $lt);
	if (!$check)
	{
		define('LEVEL_FALLBACK', true);
		if (!defined('AUTH_CHECKED'))
		{
			echo lang('auth.not_needed_level') . make_link('@root', lang('back_to_index'));
			define('AUTH_CHECKED', true);
		}
	}
	return $check;
}

/**
 * returns a lang key
 *
 * @package Helper
 * @subpackage I18n
 *
 * @param string $key The key to search
 * @param string $namespace the namespace containing the string, as
 *  /langs/[LANG]/[NAMESPACE].php
 * @param string $default the default value if the key doesn't exists
 *
 * @return string The key translated or "Untranslated"
 */
function lang($key, $namespace = 'common', $default = '%%key%% (Untranslated)')
{
	global $langs, $member;
	static $lang = NULL;
	if ($lang === NULL)
	{
		if (!isset($langs[$member->getLang()]))
			$langs[$member->getLang()] = array();
		$lang = $langs[$member->getLang()];
	}
	$l = $lang;
	if (!isset($lang[$namespace]))
	{
		$lang[$namespace] = load(sprintf('lang_%s_%s', $member->getLang(), $namespace));
	}
	$l = $lang[$namespace];
	if ($key === NULL)
		return $l;

	return !empty($l[$key]) ? $l[$key] :
	( $default === true ? NULL : str_replace('%%key%%', $key, $default) );
}

/**
 * inserts partially a template
 *
 * @package Helper
 * @subpackage Template
 *
 * @param string $name Name (location) of the partial
 * @param array $sandbox Variables on the partial
 * @param boolean $act Template or Controller partial ?
 *
 * @return void
 */
function partial($name, $sandbox = PARTIAL_FULL, $act = PARTIAL_TPL)
{
	global $router, $member, $account;
	if ($act == PARTIAL_TPL)
		$path = 'tpl';
	else
		$path = ROOT_ACTIONS . $router->getController() . DS;

	global $connected, $member;
	if ($sandbox >= PARTIAL_SEMI)
		global $layout;
	if ($sandbox >= PARTIAL_FULL)
		global $config;

	foreach ((array) $sandbox as $h)
		global ${$h};

	require $path . DS . $name . EXT;
}

/**
 * renders array to HTML attributes
 *
 * @package Helper
 * @subpackage HTML
 *
 * @param array $opt attributes to render (array)
 *
 * @return string the attributes rendered (HTML)
 */
function attributes(array $opt = array())
{
	if (!$opt) //empty array, NULL, false ...
		return '';
	$opts = '';
	$second = array();
	foreach ($opt as $key => $value)
	{
		if (is_array($value) && !is_string($key))
		{ //adding new values
			$second += $value;
			continue;
		}
		else
		{
			if (is_array($value) && $key == 'style')
			{
				$value = array_to_style($value);
			}
			$opts .= sprintf(' %s="%s"', $key, $value);
		}
	}
	$opts .= attributes($second); //values added on-the-fly
	return $opts;
}

/**
 * formats an array to a style declaration
 *
 * @package Helper
 * @subpackage HTML
 *
 * @param $ary CSS params
 *
 * @return string css-valid declaration(s)
 */
function array_to_style(array $ary)
{
	$style = '';
	foreach ($ary as $k => $v)
		$style .= sprintf('%s: %s; ', $k, $v);
	return $style;
}
 
/**
 * creates a HTML tag
 *
 * @package Helper
 * @subpackage HTML
 *
 * @param string $n Name of the tag, as "a"
 * @param array $opt HTML attributes for the tag
 * @param string $c -default:NULL Content of the tag
 * @return string HTML rendered
 */
function tag($name, $opt = array(), $content = NULL)
{
	if ($content === NULL && !is_array($opt))
	{
		$content = $opt;
		$opt = array();
	}

	$end = $content === NULL || $name === 'img' || $name === 'br' ? ' />' :
			'>' . $content . '</' . $name . '>';
	return tag_open($name, $opt) . $end;
}

/**
 * creates the beginning of a HTML tag (<{name} {opts}>)*
 *
 * @param string $name tag name
 * @param array $opt HTML options
 *
 * @return string opening tag
 */
function tag_open($name, $opt = array())
{
	return '<' . $name . attributes($opt);
}

/**
 * adds an "meta" tag to the header
 *
 * @package Helper
 * @subpackage HTML
 *
 * @param string $equiv Meta's http-equiv
 * @param string $content Meta's content
 */
function meta($equiv, $content)
{
	global $metas;
	$metas[$equiv] = $content;
}

/**
 * includes all stylesheets files (params, @see func_get_args)
 *
 * @package Helper
 * @subpackage HTML
 *
 * @return void
 */
function stylesheet_tag()
{
	global $config;
	$args = func_get_args();
	$stylesheets = '';
	$ext = '.css';
	foreach ($args as $css)
	{
		$url = strpos($css, 'http://') === 0 ? $arg : getPath() . 'static/templates/' . $config['template'] . '/css/' . str_replace($ext, '', $css) . $ext;
		$stylesheets .= "\t\t\t" . '@import url("' . $url . ( DEBUG ? '?' . rand(0, 50) : '' ) . '");' . "\n";
	}
	echo tag('style', array('type' => 'text/css'), "\n$stylesheets\t\t");
}

/**
 * includes all javascripts files (params, @see func_get_args)
 *
 * @package Helper
 * @subpackage HTML
 *
 * @return void
 */
function javascript_tag()
{
	$args = func_get_args();
	$jsFiles = '';
	$ext = '.js';
	foreach ($args as $js)
	{
		$url = strpos($js, 'http://') === 0 ? $js : getPath() . 'static/js/' . str_replace($ext, '', $js) . $ext;
		$jsFiles .= "\n\t\t" . tag('script', array('type' => 'text/javascript', 'src' => $url), '');
	}
	echo $jsFiles;
}

/**
 * creates javascript tag and CDATA plus puts JS in
 *
 * @package Helper
 * @subpackage HTML
 *
 * @param string $c JS Content
 * @param array $opt Attributes to add to the tag
 * @return string script{type:text/javascriipt} tag with the content
 */
function js($c = NULL, $opt = array())
{
	static $opened = false;
	$type = array('type' => 'text/javascript');
	if ($c !== NULL)
	{
		return tag('script', $opt + $type, $c);
	}
	else
	{
		if ($opened)
			$html = "\n</script>";
		else
			$html = '<script' . attributes($type) . ">\n";
		$opened = !$opened;
		return $html;
	}
}

/**
 * formats a string PHP => JS
 *
 * @param string $str HTML value
 * @return string JS Value
 */
function javascript_string($str, $quote = "'")
{
	$str = str_replace($quote, '\\' . $quote, $str);
	return str_replace(array("\n", "\r", "\t", "\r\n"), '', $str);
}

/**
 * formats a mixed val to it's JavaScript value
 *
 * @param mixed $val the value to convert
 * @return string $val in JS format
 */
function javascript_val($val)
{
	if (is_integer($val))
		return $val;
	if (is_bool($val))
		return $val ? 'true' : 'false';
	if (is_string($val)
	 || ( is_object($val) && method_exists($val, '__toString') ))
		return '"' . javascript_string('' . $val, '"') . '"'; //force string conversion
	if (is_array($val))
	{
		$js = array();
		foreach ($val as $k => $v)
			$js[] = "'" . javascript_string($k) . "': " . javascript_val($v);
		return '{' . implode(', ', $js) . '}';
	}
	else //object, callable, anything else you want ...
		return 'null';
}

/**
 * creates script tag plus puts CSS in
 *
 * @package Helper
 * @subpackage HTML
 *
 * @param string $c CSS Content
 * @param array $opt Attributes to add to the tag
 * @return string style{type:text/css} tag with the content
 */
function css($c, $opt = array())
{
	return tag('style', array_merge($opt, array('type' => 'text/css')), $c);
}

/**
 * encodes params plus formates the URL
 *
 * @global Router $router
 * @param array $params Params to encode
 * @param boolean $strict Strict encoding ? (&amp;) or no ?
 * @param boolean $rewrite Rewrite the URL ?
 * @return string URL
 */
function to_url($params, $strict = true, $rewrite = true)
{
	global $router;
	if (empty($params))
		return '';
	$url = encode_url_params($params, $strict, $rewrite);
	return $rewrite ? getPath(( $router->rewrite() ? '' : '?' ) . $url) : '?' . $url;
}

/**
 * replaces special urls
 *
 * @package Helper
 * @subpackage Route
 *
 * @example echo replace_url('@root');
 *
 * @param string $url URL to change
 * @return string URL modified
 */
function replace_url($url, $strict = true)
{
	global $routes;
	if (!is_string($url))
	{
		if (is_array($url))
			return to_url($url);
		else
			return $url;
	}
	$_url = substr($url, 1);
	if (is_string($url) && substr($url, 0, 1) === '@' && isset($routes[$_url]))
	{
		$url = $routes[$_url];
		if ($url === NULL)
			$url = array();
		if (!isset($url['action']))
			$url['action'] = $_url; //too magic :x

	}
	if (is_array($url))
	{
		$url = to_url($url, $strict);
	}
	return $url;
}

/**
 * returns the path depending on rewrite on/off
 *
 * @package Router
 * @subpackage Rewrite
 *
 * @example string $url the URL
 *
 * @return string script's path
 */
function getPath($url = NULL)
{
	global $router;
	static $absPath = NULL;
	if ($absPath === NULL)
	{
		if (isset($_SERVER['SCRIPT_NAME']))
		{
			$abs = explode(DS, $_SERVER['SCRIPT_NAME']);
			unset($abs[count($abs) - 1]);
			$abs = implode(DS, $abs);
		}
		else
			$abs = '.' . DS;
	}
	$p = '';
	if ($router->rewrite())
	{
		$p = $abs . DS;
	}
	if ($url === NULL)
		return $p;
	if ($url[0] === '/')
		$url = substr($url, 1);

	return ( substr($url, 0, 4) === 'http' ? '' : $p ) . $url;
}

/**
 * redirects to a page
 *
 * @package Router
 * @subpackage Location
 *
 * @param $loc string|null the url to redirect
 * @param $wait double wait how many seconds before redirect ? 0 for direct
 * @return void
 */
function redirect($loc = NULL, $wait = 0)
{
	global $router;
	if ($loc === NULL)
	{
		$loc = '@root';
	}
	if (is_array($loc))
		$loc = to_url($loc, false);
	$loc = replace_url($loc, false);
	$wait = doubleval($wait);

	if (!$router->isAjax())
	{
		if (!$wait)
			header('Location: ' . $loc);
		meta('refresh', $wait . '; url=' . $loc);
	}
	$js = js(sprintf('document.setTimeout(function () { document.location = "%s"; }, %d)', $loc, $wait == 0.0 ? 0 : $wait * 1000));
	if ($wait)
		echo $js;
	else
		exit($js);
}

/**
 * pluralizes a string
 *
 * @package Helper
 * @subpackage Grammar
 *
 * @param string $word The word to pluralize
 * @param integer $i Count
 * @param boolean $show_i Show $i?
 *
 * @return string String pluralized (or not)
 */
function pluralize($word, $i = 0, $show_i = false, $add = '%%content%% ')
{
	return ( $show_i ? str_replace('%%content%%', $i, $add) : '' ) . $word . ( $i > 1 ? 's' : '' );
}

/**
 * creates an image
 *
 * @package Helper
 * @subpackage HTML
 *
 * @param string $url URL of image
 * @param string $title attributes.title
 * @param string $alt attributes.alt
 * @param array $style attributes.style.join " "
 *
 * @return string the image
 */
function make_img($url, $ext = EXT_JPG, $title = NULL, $alt = NULL, $add = array())
{
	if (is_array($title))
	{ //title = add
		$add = $title;
		$title = NULL;
	}
	else if (is_array($alt))
	{ //alt = add
		$add = $alt;
		$alt = '';
	}
	return tag('img', $add + array('src' => url_for_image($url, $ext), 'title' => $title, 'alt' => $alt));
}

/**
 * returns the url for an image
 *
 * @package Helper
 * @subpackage HTML
 *
 * @param string $url the image (relative or not) url
 * @param string $ext the image extension
 */
function url_for_image($url, $ext = EXT_JPG)
{
	global $config;
	static $ignores = array('items');
	if (substr($url, 0, 4) !== 'http' && substr($url, 0, 7) !== 'file://')
	{
		$template = 'templates/' . $config['template'] . '/';
		foreach ($ignores as $ignore)
		{
			if (strpos($url, $ignore) === 0)
			{
				$template = '';
				break; //stop here, we found what we need
			}
		}
		$url = getPath() . 'static/' . $template . 'images/' . $url;
	}
	return $url . '.' . $ext;
}

/**
 * stocks jQuery Code or creates a slot for (ob)
 *
 * @package Helper
 * @subpackage HTML
 *
 * @param string $put jQuery code to put
 * @return string actual jQuery code
 */
function jQ($put = NULL, $in = false)
{
	static $i = false;
	if ($i == false)
		$i = true;
	static $_jQs = array(),
		$_starteds = array(),
		$actual_in = 'main';

	if ($in === false)
		$in = $actual_in;

	if (empty($_jQs[$in]))
	{
		$_jQs[$in] = '';
		if (is_string($put))
			$_starteds[$in] = false;
	}

	if ($put === true)
	{
		$_starteds[$in] = true;
		ob_start();
	}
	else if ($put === NULL)
	{
		if (!empty($_starteds[$in]))
		{
			$_starteds[$in] = false;
			$put = ob_get_clean();
			$put = str_replace(array('<script type="text/javascript">', '</script>'), '', $put);
		}
		else
		{
			$js = $_jQs[$in];
			$_jQs[$in] = '';
			return $js;
		}
	}
	else if ($put === false)
	{ //default in.
		$actual_in = $in;
	}
	if (substr(rtrim($put), -1) != ';' && !empty($put) && $put !== true)
		$put .= ';';
	if (is_string($put)) {
		$_jQs[$in] .= "\n" . $put;
	}
	return $_jQs[$in];
}

/**
 * creates a javascript link
 *
 * @package Helper
 * @subpackage HTML
 *
 * @param string $js JS
 * @param string $text Text of the link
 * @param string $link The link if ! JS.activated?
 * @param array $add HTML properties
 * @param string $event Event (@example click)
 *
 * @return string the JS Link
 */
function js_link($js, $text, $link = '#', $add = array(), $event = NULL)
{
	if (is_array($js))
	{
		$in = $js[1];
		$js = $js[0];
	}
	else
		$in = false;

	static $JSs = array();
	if (!$event)
		$event = 'click';
	$event = (array) $event;
	do
	{
		$id = $memId = rand(0, 10000);
	} while (isset($JSs[$id]));
	$js = substr($js, 0, -1) == ';' ? $js : $js . ';';
	$jsCode = sprintf('
	var action' . $id . ' = function (event)
	{
		' . $js . '
		event.preventDefault();
	}');
	jQ('
	var loc' . $id . ' = $("#link_' . $id . '");', $in);
	if (( $jsID = array_search($js, $JSs) ) !== false)
		$id = $jsID;
	else
	{
		jQ($jsCode, $in);
		$JSs[$id] = $js;
	}
	foreach ($event as $ev)
	{
		$ev = strtolower($ev);
		$jsCode = 'loc' . $memId . '.attr("href", "#")'; //yeah, this might be slow, but it's the best to keep it nojs-compatible
		jQ('locations["link_' . $memId . '"] = ' . javascript_val($link) . ';', $in);
		$jsCode .= sprintf('.live( "%s", action%d )', $ev, $id);
		jQ($jsCode, $in);
	}
	return tag('a', $add + array('href' => $link, 'id' => 'link_' . $id), $text);
}

/**
 * creates a link
 *
 * @package Helper
 * @subpackage HTML
 *
 * @param string $url(|$opt) Url of the link
 * @param string $n Text for the link
 * @param array $opt URL Params
 * @param boolean $js active JS link ?
 *
 * @return string formatted link
 */
function make_link($_url, $n, $opt = array(), $add = array(), $js = true)
{
	global $config;
	if ($config['LOAD_TYPE'] == LOAD_NONE)
		$js = false; //force JS to be off

	$url = replace_url($_url);
	if (is_array($url) && ( $opt === array() || $opt === NULL ))
	{
		$opt = $url;
		$url = '';
	}

	if ($js)
		$link = array('class' => 'link');
	else
		$link = array();
	return tag('a',
		array_merge($link, $add, array('href' => $url . ( empty($opt) ? '' : to_url($opt) ))),
		$n);
}

/**
 * formats an url
 *
 * @param string $url adress of page
 *
 * @return string the URL
 */
function make_url($url, $params, $strict = true)
{
	if (is_array($url))
	{
		$params = $url;
		$url = '';
	}
	return $url . ( empty($params) ? '' : '?' ) . encode_url_params($params, $strict, false);
}

/**
 * encodes an url param
 *
 * @param string $val The param to encode
 * @return string urlencode(param) unless param startsWith and endsWith '%%'
 */
function encode_url_param($val)
{
	if (substr($val, 0, 2) === '%%' && substr($val, -2) === '%%') //hack for js ...
	{
		return '" + ' . substr($val, 2, -2) . ' + "';
	}
	else
	{
		return urlencode($val);
	}
}

/**
 * encodes the params to URL
 *
 * @see make_link
 * @see redirect
 *
 * @package Helper
 * @subpacekage Route
 *
 * @param array $params The params to convert to URL
 * @param boolean $strict &amp; or just & ?
 * @param boolean $rewrite try to rewrite the URL?
 *
 * @return string parameters as string
 */
function encode_url_params(array $params = array(), $strict = true, $rewrite = true)
{
	global $router;
	$url = '';
	if (empty($params))
		return getPath();
	if (!isset($params['controller']) && isset($params['action']))
		$params['controller'] = $router->getDefault('controller');
	/*
	  foreach( array( 'controller', 'action' ) as $use )
	  if( isset( $params[$use] ) )
	  $params[$use] = call_user_func( array( 'Router', 'get' . ucfirst( $use ) . 'Use' ), $params[$use] );
	 */ //consider that we are local only ^^'
	if (isset($params['controller']))
		$params['controller'] = $router->getControllerUse($params['controller']);
	if (isset($params['action']))
		$params['action'] = $router->getActionUse($params['action']);
	$ext = '';
	if (isset($params['ext']) && $router->rewrite() && $rewrite)
	{
		$ext = $router->getExtFor($params['ext']);
		unset($params['ext']);
	}

	foreach ($params as $key => $param)
	{ //rewrite all params, rewriting or not.
		$params[$key] = encode_url_param($param);
	}

	if ($router->rewrite() && $rewrite)
	{
		$keys = array_keys($params);
		sort($keys); //to compare, later
		if (isset($params['controller']))
			$url .= $params['controller'];
		if ($keys == array('controller'))
			return $url . $ext;

		if (isset($params['action']))
			$url .= DS . $params['action'];
		if ($keys == array('action', 'controller'))
			return $url . $ext;

		if ($keys == array('action', 'controller', 'id'))
			return $url . DS . $params['id'] . $ext;

		if ($keys == array('action', 'controller', 'id', 'mode') && is_numeric($params['id']))
			return $url . DS . $params['id']
			. DS . $params['mode'] . $ext;
		unset($params['controller'], $params['action']);
		foreach ($params as $k => $v)
			$url .= DS . $k . DS . $v;
		return $url . $ext;
	}

	$and = $strict ? '&amp;' : '&';
	foreach ($params as $param => $val)
		$url .= $param . '=' . $val . $and;
	return substr($url, 0, -( strlen($and) ));
}

/**
 * paginates
 *
 * @package Helper
 * @subpackage HTML
 *
 * @param Doctrine_Pager_Layout $pager layout to display
 */
function paginate(Doctrine_Pager_Layout $layout, $sep = ' ')
{
	$pager = $layout->getPager();
	$navigation = '';
	if ($pager->haveToPaginate())
	{
		$uri = $layout->getUrlMask();
 
		// First and previous page
		if ($pager->getPage() != 1)
		{
			$navigation .= make_link($uri.'1', make_img('first', EXT_PNG, array('align' => 'absmiddle')));
			if ($pager->getPage() != 2)
			{ //if it's the second page, this link is useless ...
				$navigation .= make_link($uri.$pager->getPreviousPage(), make_img('previous', EXT_PNG, array('align' => 'absmiddle')));
			}
			$navigation .= $sep;
		}
 
		// Pages one by one
		$links = array();
		foreach ($layout->getPagerRange()->rangeAroundPage() as $page)
		{
			if ($pager->getPage() == $page)
				$links[] = tag('b', $page);
			else
				$links[] = make_link($uri.$page, $page);
		}
		$navigation .= implode('   ', $links);
 
		// Next and last page
		if ($pager->getPage() != $pager->getLastPage())
		{
			if ($pager->getLastPage() != $pager->getNextPage())
			{ //the "last" & "next" page are the same
				$navigation .= $sep.make_link($uri.$pager->getNextPage(), make_img('next', EXT_PNG, array('align' => 'absmiddle')));
			}
			$navigation .= make_link($uri.$pager->getLastPage(), make_img('last', EXT_PNG, array('align' => 'absmiddle')));
		}
	}
 
	return $navigation;
}

/**
 * creates an input
 *
 * @package Helper
 * @subpackage HTML
 *
 * @param string $name Name of input
 * @param string $label Label of input
 * @param string $type Type of input
 * @param string $value Value of input
 * @param array $add Attributes for input
 *
 * @return string label, input and it's attributes
 */
function input($name, $label, $type = NULL, $value = '', $add = array())
{
	$act_label = false;
	$args = func_get_args();

	if (is_array($value) && $add === array() && $type !== 'select' && $type !== 'date_range')
	{ //replace $add & $value IF it's not a (select || date_range) 
		$add = $value;
		$value = '';
	}
	if (!$type)
	{ //by default
		$type = 'text';
	}
	if ($type === 'submit' && $label !== NULL)
	{ //If it's a submit and we have $value instead of $label
		$value = $label;
		$label = NULL;
	}
	$type = strtolower($type); //Conventions

	if ($type !== 'select')
	{
		if (!is_array($add) && isset($args[0]) && is_array($args[0]))
		{ //if add is not array, then add is the default select's value. Then, try to take $add from $args[0] (the arg after $add)
			$selected = $add;
			$add = $args[0];
		}
	}
	else
	{
		if (is_array($add))
		{
			foreach (array('type', 'name', 'value') as $n)
			{
				if (isset($add[$n]))
				{
					unset($add[$n]);
				}
			}
		}
		if (isset($args[0]))
		{ //act as a label ?
			$act_label = (bool) $args[0];
		}
	}

	$value = str_replace('</', '&lt;/', $value); //On vire les caract�res sp�ciaux de $value
	if ($type === 'textarea')
	{
		$type = tag('textarea', $add + array($add, 'name' => $name, 'id' => 'form_' . $name), $value);
	}
	elseif ($type === 'select')
	{
		if (!is_array($add) && !isset($selected))//selected
		{
			$selected = intval($add);
		}
		else
		{
			$selected = empty($args[0]) ? NULL : $args[0];
		}
		$type = tag('select', array('name' => $name, 'id' => 'form_' . $name), input_select_options($value, $selected));
	}
	else
	{
		$pre_html = $post_html = '';
		global $calendar_opts;
		switch ($type)
		{
			case 'date':
				if (is_numeric($value))
					$value = date('d/m/Y', $value);
				$type = 'text';
				jQ(sprintf('$("#form_%s").datepicker(
					{
						%s,
					});', $name, $calendar_opts));
			break;

			case 'datetime':
				if (!empty($value))
					$value = datetime_to_picker($value);
				$type = 'text';
				$_opts = array();
				if (isset($add['__restrict']))
				{
					$restrict = $add['__restrict'];
					if (!is_array($restrict))
						$res = explode(';', $restrict);

					$restrict = array();
					if (!empty($res[0]))
						$restrict['minDate'] = $res[0];
					if (!empty($res[1]))
						$restrict['minDate'] = $res[1];
					foreach ($restrict as $t => &$res)
					{
						if ($res == '@today')
							$res = date('j') . '/' . date('m') . '/' . date('Y');
						if ($res == '@today+')
							$res = ( date('j') + 1 ) . '/' . date('m') . '/' . date('Y');
					}

					$_opts += $restrict;
					unset($add['restrict']);
				}
				jQ(sprintf('$("#form_%s").datetimepicker($.extend({%s}, %s));', $name, $calendar_opts, json_encode($_opts)));
			break;
			case 'date_range':
				if (is_numeric($value))
					$value = date('d/m/Y', $value);
				static $date_range_i = -1; //multiple dateranges on the same page.
				if ($date_range_i == -1)
				{ //first date range
					jQ('dates = {};'); //create the date ranges array
				}
				$type = 'text';
				if (is_array($name))
				{
					$to_name = empty($name[1]) ? $name . '2' : $name[1];
					$name = $name[0];
				}
				else
					$to_name = $name . '2';

				jQ(sprintf('dates[%d] = jQuery("#form_%s, #form_%s").datepicker(
					{
						%s,
						changeMonth: true,
						onSelect: function (selectedDate)
							{
								var option = this.name == "%3$s" ? "minDate" : "maxDate",
									instance = $( this ).data( "datepicker" );
									date = $.datepicker.parseDate(
										instance.settings.dateFormat ||
										$.datepicker._defaults.dateFormat,
										selectedDate, instance.settings );
								dates[%1$d].not( this ).datepicker( "option", option, date );
							}
					});', ++$date_range_i, $to_name, $name, $calendar_opts));
				if (is_array($label))
				{
					$to_label = empty($label[1]) ? lang('date_to') : $label[1];
					$label = $label[0]; //replace
				}
				else
					$to_label = lang('date_to');
				if (is_array($value))
				{
					$to_value = empty($value[1]) ? '' : date_to_picker($value[1]);
					$value = $value[0];
				}
				else
					$to_value = '';
				$post_html = '&nbsp;' . input($to_name, $to_label, NULL, $to_value);
				if (!empty($value))
				{
					if (!( $value = @date('d/m/Y', $value) ))
					{
						$value = false;
					}
				}
			}
			$params = array('type' => $type, 'name' => $name, 'value' => $value, 'id' => 'form_' . $name);
			if ($type === 'checkbox')
			{
				if ((bool) $value)
				{
					$params = array_merge($params, array('checked' => 'checked'));
				}
				$value = 'on';
			}
			$type = $pre_html . tag('input', array_merge($params, $add)) . $post_html;
	}

	$opts_label = array('for' => 'form_' . $name);
	if ($act_label && is_array($add))
	{
		$opts_label = array_merge($opts_label, $add);
	}
	return (empty($label) ? '' : tag('label', $opts_label, $label)) . $type;
}

/**
 * generates options for a select tag
 *
 * @package Helper
 * @subpackage HTML
 *
 * @param array $value values
 * @param mixed $selected the actual selection
 *
 * @return string HTML for this select
 */
function input_select_options($value, $selected = NULL)
{
	if ($selected == NULL)
		$selected = array_slice($value, 0, 1);
	$in_optgroup = false;
	$optgroup_label = $cache = $actual = $options = '';

	foreach ($value as $val => $title)
	{
		if ($val < -1) //allowing -1 as blank :p
		{ //add an OptGroup
			if ($in_optgroup)
			{
				$options .= tag('optgroup', array('label' => $optgroup_label), $cache);
				$cache = '';
			}
			$in_optgroup = true;
			$optgroup_label = $title;
			continue;
		}

		$o = array('value' => str_replace('\\', '', $val));
		if ($val == $selected)
		{ //the actual val.
			$o += array('selected' => 'selected');
		}
		$actual = tag('option', $o, $title);

		if ($in_optgroup)
			$cache .= $actual;
		else
			$options .= $actual;
	}
	if ($in_optgroup) //fix if we have just 1 optgroup
	{
		$options .= tag('optgroup', array('label' => $optgroup_label), $cache);
		$cache = '';
	}
	return $options;
}

/**
 * makes a list from an array
 *
 * @package Helper
 * @subpackage HTML
 *
 * @param array $list List of Options
 * @param array $optUl HTML options for the ul tag
 * @param array $optLi HTML options for the li tag
 *
 * @return the list rendered
 */
function to_list(array $list, $optUl = array(), $optLi = array())
{
	$listStr = '';
	foreach ($list as $item)
	{
		$listStr .= tag('li', $optLi, $item);
	}
	return tag('ul', $optUl, $listStr);
}

/**
 * formats error
 *
 * @package Helper
 * @subpackage Error
 *
 * @param string|NULL $for Namespace
 *
 * @return string Error, to show
 */
function render_errors($for = NULL)
{
	global $errors;
	$str = '';
	$err = $errors;
	if ($for !== NULL)
	{
		$err = $err[$for];
	}
	if (!empty($errors))
	{
		$str = "<ul>\n";
		foreach ($errors as $error)
		{
			$str .= "\t<li>\n\t\t<span style=\"color: red;\">\n\t\t\t&bull;&nbsp;" . $error . "\n\t\t</span>\n\t</li>\n";
		}
		$str .= '</ul>';
	}

	//clean errors
	$errors = array();
	return $str;
}

/**
 * creates a form
 *
 * @package Helper
 * @subpackage HTML
 *
 * @param array $columns The columns
 * @param boolean $tag Add the "form" tag ?
 *
 * @return string the form rendered
 */
function make_form($columns, $tag = NULL, $loc = '#', $opts = array())
{
	global $router;
	if (is_string($tag))
		$loc = $tag;
	if ($tag === NULL)
	{ //@ignore
		echo 'not set the append_form_tag? constant in ';
		debug_print_backtrace();
		$tag = APPEND_FORM_TAG;
	}
	if (!is_bool($tag) && $loc == '#')
	{ //loc may be a string, an array, ...
		$loc = $tag;
		$tag = APPEND_FORM_TAG;
	}
	else if (is_array($loc))
	{
		$opts = $loc;
		$loc = '#';
	}
	if (!isset($opts['method']))
		$opts['method'] = 'POST';
	if (!isset($opts['sep_inputs']))
		$opts['sep_inputs'] = tag('br');

	$firstCol = '';

	$str = render_errors();
	$temp = '';
	$first = true;
	foreach ($columns as $i => $column)
	{
		if (empty($column))
			continue;
		if (is_numeric($i))
		{
			if (empty($firstCol))
				$firstCol = $column[0];
			$br = isset($column[5]) ? $column[5] : true;
			$str .= input($column[0], $column[1], !empty($column[2]) ? $column[2] : NULL, isset($column[3]) ? $column[3] : '', !empty($column[4]) ? $column[4] : array())
					. ( $br ? $opts['sep_inputs'] : '' );
		}
		else
		{
			$count = count($column) - 1;
			$randKey = rand(0, 50);
			//fieldSet
			foreach ($column as $actual => $col)
			{
				if (empty($col))
					continue;
				if (empty($firstCol))
					$firstCol = is_array($col[0] ? $col[0][0] /*I.E. date range*/ : $col[0]);
				if (is_array($col)) //we have to create a new input
					$temp .= input($col[0], $col[1], !empty($col[2]) ? $col[2] : NULL, !empty($col[3]) ? $col[3] : '', !empty($col[4]) ? $col[4] : array()) .
							( $actual == $count ? $opts['sep_inputs'] : '' );
				else //just add HTML to the form
					$temp .= $col;
			}
			//@todo: a way to return the fs key ? =|.
			$str .= tag('fieldset', array('id' => 'fs_' . $randKey),
						tag('legend', tag('span', array('name' => $i, 'class' => 'slideMenu'), $i)) .
						tag('div', array('class' => 'inputs'), $temp));
			$temp = ''; //reset the temp data
		}
	}
	unset($temp);
	if ($loc === '#')
	{
		$params = array(
			'controller' => $router->getController(),
			'action' => $router->getAction(),
		);
		if (( $id = $router->requestVar('id', NULL) ) !== NULL)
			$params += array('id' => $id);
		$loc = to_url($params);
		jQ(sprintf('$( "#form_%s" ).focus();', $firstCol));
	}
	else
		$loc = replace_url($loc);
	$str .= input('send', lang('send'), 'submit') . input('sent', NULL, 'hidden', 1);
	return $tag ? tag('form', array('method' => $opts['method'], 'action' => $loc, 'id' => 'form'), $str) : $str;
}

/**
 * security for show (who said "uglyyyyy" :x ?!)
 *
 * @package Helper
 * @subpackage HTML
 *
 * @param string $str String to securise
 *
 * @return string Securised string
 */
function html($str)
{
	return htmlentities($str, ENT_QUOTES, 'UTF-8');
}

/**
 * date_passed
 *
 * @return boolean @see time_passed
 */
function date_passed()
{
	$args = func_get_args();
	return call_user_func_array('time_passed', array_merge(array(time()), $args));
}

/**
 * time_passed
 *
 * @param integer the date
 *
 * @return boolean passed or not
 */
function time_passed($from)
{
	$time = 0;
	foreach(array_slice(func_get_args(), 1)  as $arg)
		$time += is_numeric($arg) ? $arg : strtotime($arg, 0);

	return $from > $time;
}

/**
 * generates a date from a datepicker
 *
 * @param string $date date to parse
 *
 * @return DateTime the datetime object
 */
function date_from_picker($date)
{
	$date = implode('-', array_reverse(explode('/', $date)));
	if (strlen($date) !== 10)
		return false;
	return date_create($date);
}
/**
 * generates a date for a datepicker
 *
 * @param string $date date to parse
 *
 * @return string the new date
 */
function date_to_picker($date)
{
	if ($date instanceof DateTime)
		$date = $date->format('Y-m-d');
	$date = implode('/', array_reverse(explode('-', $date)));
	if (strlen($date) !== 10)
		return false;
	return $date;
}
/**
 * generates a datetime from a datetimepicker
 *
 * @param string $date date+time to parse
 *
 * @return DateTime the datetime object
 */
function datetime_from_picker($date)
{
	list($date, $time) = explode(' ', $date);
	$time = explode(':', $time);
	if (count($time) < 2)
		return false;
	if (!$date = date_from_picker($date))
		return false;

	$date = $date->modify('+' . $time[0] . ' hours');
	$date = $date->modify('+' . $time[1] . ' minutes');
	return $date;
}
/**
 * generates a datetime for a datepicker
 *
 * @param string $date datetime to parse
 *
 * @return string the new datetime
 */
function datetime_to_picker($datetime)
{
	if ($datetime instanceof DateTime)
		$datetime = $date->format('Y-m-d H:i');
	list($date, $time) = explode(' ', $datetime);
	$date = implode('/', array_reverse(explode('-', $date)));
	if (strlen($date) !== 10)
		return false;
	$time = explode(':', $time);
	if (count($time) < 2)
		return false;
	$time = implode(':', array($time[0], $time[1]));
	return $date . ' ' . $time;
}

/**
 * converts a string from the base $base to a decimal
 *
 * @param string $string base-{$base} encoded string
 * @param integer $base the base (from 2 to 36, except 10)
 *
 * @return integer
 */
function str2dec($string, $base)
{
	$decimal = 0;
	$base = (int) $base;
	if ($base == 10)
		return $string; //for those who don't know that, base 10 is the "common" base ...
	if ($base < 2 || $base > 36)
		return '$base must be between 2..36.';
	$charset = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charset = substr($charset, 0, $base);
	$string = trim($string);
	if (empty($string))
		return 0;

	do
	{
		$char = $string[0];
		$string = substr($string, 1);
		$pos = strpos($charset, $char);
		if (( $pos = strpos($charset, $char) ) === false)
			return sprintf('Illegal character (%s) in $string', $char);
		$decimal = ( $decimal * $base ) + $pos;
	}
	while ($string != NULL);

	return $decimal;
}

function load_models($cat)
{
	$cat = 'models/' . $cat . '/php/';
	Doctrine_Core::loadModels($cat . 'generated/');
	Doctrine_Core::loadModels($cat);
	set_include_path(get_include_path() . PATH_SEPARATOR . $cat . PATH_SEPARATOR . $cat . 'generated/');
}

/**
 * __shutdown
 * Function called at the end of the program
 * /!\ WARNING /!\:
 * This function cannot be registered with register_shutdown_function because PHP does not accept
 *  Exceptions to be throw in shutdown functions
 *
 * @global Account $account Account to save
 *
 * @return void
 */
function __shutdown()
{
	global $router, $account;
	if (level(LEVEL_LOGGED))
	{
		if ($account !== NULL && $account->relatedExists('User'))
		{
			$account->save();
		}
		if (DEBUG && !$router->isAjax())
			echo '<!-- Saving... -->';
	}
}

if (DEBUG && !DEV)
	$mem .= memory_get_usage() . ': Helpers charges - fonctions<br />';
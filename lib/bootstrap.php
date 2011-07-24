<?php
global $config, $errors, $metas, $types, $account;

/**
 * @file $Id: bootstrap.php 56 2011-01-16 19:39:32Z nami.d0c.0 $
 *
 * Contains business for application loading
 */

//Directory Separator (DIRECTORY_SEPARATOR)
define('DS', '/');


//I created this constants because I hate "ghost parameters"
//@see make_form
/* * Append the <form> & </form> */
define('APPEND_FORM_TAG', true);
/* * Don't append the <form> & </form> */
define('NOT_APPEND_FORM_TAG', false);

/* * Reverse level */
define('REQUIRE_NOT', true);

//@see encode_url_params
/* * Append the ? */
define('APPEND_QUESTION_MARK', true);
/* * Don't append the ? */
define('NOT_APPEND_QUESTION_MARK', false);

//@see parse_hour
/* * A second */
define('TYPE_DATE_SECOND', 1);
/* * A minute */
define('TYPE_DATE_MINUTE', TYPE_DATE_SECOND * 60);
/* * A hour */
define('TYPE_DATE_HOUR', TYPE_DATE_MINUTE * 60);
/* * A day */
define('TYPE_DATE_DAY', TYPE_DATE_HOUR * 24);
/* * A week */
define('TYPE_DATE_WEEK', TYPE_DATE_HOUR * 7);

//@see partial
/* * Mini-authorized partial */
define('PARTIAL_SANDBOXED', 0);
/* * Semi-authorized partial */
define('PARTIAL_SEMI', 1);
/* * Full-authorized partial */
define('PARTIAL_FULL', 2);

/* * A TPL */
define('PARTIAL_TPL', false);
/* * A controller */
define('PARTIAL_CONTROLLER', true);

//@see pluralize
/* * Show the pluralized value */
define('SHOW_VALUE', true);

defined('DEBUG') || define('DEBUG', ( isset($_SERVER['SESSIONNAME']) && $_SERVER['SESSIONNAME'] === 'Console' )
				|| $_SERVER['REMOTE_ADDR'] === '127.0.0.1');

//CMS Version
define('VERSION', '1.3.0');

//Some extensions
define('EXT_GIF', 'gif');
define('EXT_JPG', 'jpg');
define('EXT_PNG', 'png');

//Page-load type
define('LOAD_NONE', -1);   //no-AJaX
define('LOAD_NOTHING', 0); //nothing
define('LOAD_CONTENT', 1); //change content
define('LOAD_MDIALOG', 2); //modal dialog
//For BugTracker (No real enums in PHP)
define('STATE_SUBMITTED', 0);
define('STATE_RESOLVING', 1);
define('STATE_RESOLVED', 2);

//User levels
define('LEVEL_BANNED', -2);
define('LEVEL_GUEST', -1);
define('LEVEL_LOGGED', 0);
define('LEVEL_TEST', 1);
define('LEVEL_MODO', 2);
define('LEVEL_MJ', 3);
define('LEVEL_ADMIN', 4);

$calendar_opts = '
					showButtonPanel: true,
					changeMonth: true,
					changeYear: true,
					showOtherMonths: true,
					selectOtherMonths: true,
					showWeek: true,
					firstDay: 1,
					showAnim: "slideDown",
					dateFormat: "dd/mm/yy"';

$routes = array(//action default : key
	'root' => array('controller' => 'News', 'action' => 'index'),

	'register' => array('controller' => 'Account', 'action' => 'new'),
	'sign_in' => array('controller' => 'Account', 'action' => 'login'),
	'sign_off' => array('controller' => 'Account', 'action' => 'delog'),
	'account.edit' => array('controller' => 'Account', 'action' => 'index'),
	'vote' => array('controller' => 'Account'),
	'credit' => array('controller' => 'Account'),
	'ladder_vote' => array('controller' => 'Account'),

	'character.give' => array('controller' => 'Character', 'action' => 'give'),

	'join' => array('controller' => 'Misc'),
	'staff' => array('controller' => 'Misc'),
	'mass_mail' => array('controller' => 'Misc'),

	'tos' => array('controller' => 'Misc'),
	'cgu_serv' => array('controller' => 'Misc', 'action' => 'tos_serv'),

	'shop' => array('controller' => 'Shop', 'action' => 'index'),

	'guestbook' => array('controller' => 'GuestBook', 'action' => 'index'),
	'guestbook.new' => array('controller' => 'GuestBook', 'action' => 'update'),

	'ladder' => array('controller' => 'Character'),
	'character.search' => array('controller' => 'Character', 'action' => 'search'),

	'polls' => array('controller' => 'Poll', 'action' => 'index'),
	'poll.new' => array('controller' => 'Poll', 'action' => 'update'),

	'ticket_category.new' => array('controller' => 'TicketCategory', 'action' => 'update'),

	'pm' => array('controller' => 'PrivateMessage', 'action' => 'index'),
	'pm.create' => array('controller' => 'PrivateMessage', 'action' => 'create'),

	'events' => array('controller' => 'Event', 'action' => 'index'),
);



require 'lib/functions' . EXT;
$config = require 'config' . EXT;

define('SERVER_STATE', @fsockopen($config['IP_SERV'], $config['PORT_SERV'], $errno, $errstr, 1) ? 'on' : 'off');

//config adjustment
if ($config['URL_VOTE'] != -1)
	$config['URL_VOTE'] = sprintf('http://www.rpg-paradize.com/?page=vote&vote=%d', $config['URL_VOTE']);
define('FORUM', isset($config['BOARD_URL']) ? $config['BOARD_URL'] : NULL);

if (!isset($config['SERVER_CORP']))
	$config['SERVER_CORP'] = '';
if (!$config['JAVASCRIPT'])
	$config['LOAD_TYPE'] = LOAD_NONE;

//disable magic_quotes (which is horrible ...) ! @ = no error (magic_quotes: deprecated)
if (function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc())
{
	$_POST = array_map('stripslashes', $_POST);
	$_GET = array_map('stripslashes', $_GET);
	$_REQUEST = array_map('stripslashes', $_REQUEST);
	$_COOKIE = array_map('stripslashes', $_COOKIE);
}
if (addslashes("'") !== "\\'")
	exit(sprintf('Pour utiliser InfiniteCMS %1$s, vous devez d&eacute;sactiver magic_quotes_sybase dans votre php.ini<br />
If you want to use InfiniteCMS %1$s, you must disable magic_quotes_sybase in your php.ini', VERSION));

if (DEBUG)
	error_reporting(-1);

//errors, metas headers
if (!DEV)
{
	$errors = $metas = array();
	$member = Member::getInstance();
	$router = Router::getInstance();
	$router->getAction();
	$act = $router->requestVar('action', 'index');
	$langs = array();
	if (!isset($config['langs']))
		$config['langs'] = array($config['use_lang']);
	//$langs[$member->getLang()]['title'] is initied when call to lang( ~, 'title )
	$langs[$member->getLang()]['title']['Misc - server'] = sprintf(lang('Misc - server', 'title'), $config['SERVER_NAME']);

	Cache::addReplacement('%lang%', $member->getLang());
	Cache::setDirFormat('%dir%/%lang%');
	Cache::ensureDir();
}

//Create DB Connexions

$login = $config['DB_TYPE'] . '://' . $config['DB_USER'] . ':' . $config['DB_PSWD'] . '@' . $config['DB_HOST'] . '/';
$doctrine = array(
	'other' => Doctrine_Manager::connection($login . $config['DB_OTHER'], 'other'),
	'static' => Doctrine_Manager::connection($login . $config['DB_STATIC'], 'static'),
);
if (DEBUG && !DEV)
	$mem .= memory_get_usage() . ': connections loaded - ' . __FILE__ . ':' . __LINE__ . '<br />';
$manager = Doctrine_Manager::getInstance();
$manager->setAttribute(Doctrine_Core::ATTR_QUERY_CLASS, 'Query');
$manager->setAttribute(Doctrine_Core::ATTR_TABLE_CLASS, 'RecordTable');
$manager->setAttribute(Doctrine_Core::ATTR_COLLECTION_CLASS, 'Collection');
$manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING,
		Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
$manager->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, true);
set_include_path(implode(PATH_SEPARATOR, array(
			ROOT, //see #0.3.2a
			'lib/class/', //local libs > global libs
#		get_include_path(),
		)));
if (DEBUG && !DEV)
	$mem .= memory_get_usage() . ': Attributes loaded ... - ' . __FILE__ . ':' . __LINE__ . '<br />';
unset($login);

if (!DEV)
{
	load_models('other');

	session_start();

	//Shop item types
	$types = array(
			ShopItemEffectTable::TYPE_LEVEL_UP => lang('shop.level_up'),
			-2 => lang('shop._add'),
			ShopItemEffectTable::TYPE_ADD_XP => lang('shop.xp'),
			ShopItemEffectTable::TYPE_ADD_K => lang('shop.K'),
			ShopItemEffectTable::TYPE_ADD_CAPITAL => lang('shop.capital'),
			ShopItemEffectTable::TYPE_ADD_SPELLPOINT => lang('shop.spellpoint'),
			-3 => lang('shop._items'),
			ShopItemEffectTable::TYPE_ITEM_JETS_ALEATOIRES => lang('shop.item_random'),
			ShopItemEffectTable::TYPE_ITEM_JETS_MAX => lang('shop.item_perfect'),
			-4 => lang('shop._stats'),
			ShopItemEffectTable::TYPE_CARAC_FORCE => lang('shop.stat.strength'),
			ShopItemEffectTable::TYPE_CARAC_AGILITE => lang('shop.stat.agility'),
			ShopItemEffectTable::TYPE_CARAC_CHANCE => lang('shop.stat.chance'),
			ShopItemEffectTable::TYPE_CARAC_SAGESSE => lang('shop.stat.wisdom'),
			ShopItemEffectTable::TYPE_CARAC_VITALITE => lang('shop.stat.vitality'),
			ShopItemEffectTable::TYPE_CARAC_INTELLIGENCE => lang('shop.stat.intell'),
		);

	if (DEBUG)
		$mem .= memory_get_usage() . ': Models loaded ... - ' . __FILE__ . ':' . __LINE__ . '<br />';

	if (!empty($_SESSION['guid']))
	{ //retrieve account
			$accountQ = Query::create()
							->from('Account a')
								->leftJoin('a.Characters c INDEXBY c.guid')
									->leftJoin('c.Events e INDEXBY e.id')
								->leftJoin('a.User u')
									->leftJoin('u.PollOptions po')
										->leftJoin('po.Poll p')
									->leftJoin('u.Review r')
							->where('guid = ?', $_SESSION['guid']);
			$account = $accountQ->fetchOne();
			$accountQ->free();
			if (!$account)
				unset($_SESSION['guid']);
			/* @var $account Account */
			if (!$account->relatedExists('User'))
			{
				$account->User = UserTable::getInstance()->fromGuid($account->guid);
			}
#			$_SESSION['account'] = serialize($account);
#		}
		if (DEBUG)
			$mem .= memory_get_usage() . ': Acc loaded ... - ' . __FILE__ . ':' . __LINE__ . '<br />';
	}
}

/* @var $account Account */
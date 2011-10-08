<?php
/** @todo
 * K The main thing to do here is to remove all those if (DEV) / if (DEBUG) calls. it makes readin harder, for nothing special
 */
global $config, $errors, $metas, $types, $account, $router, $member, $guildRights;

/**
 * @file $Id: bootstrap.php 56 2011-01-16 19:39:32Z nami.d0c.0 $
 *
 * Contains business for application loading
 */

//Directory Separator (DIRECTORY_SEPARATOR)
define('DS', '/');


//I created this constants because I hate "ghost parameters"

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
define('LEVEL_BANNED', -2.0);
define('LEVEL_GUEST', -1.0);
define('LEVEL_LOGGED', 0.0);
define('LEVEL_VIP', 0.5); //[...]
define('LEVEL_TEST', 1.0);
define('LEVEL_MODO', 2.0);
define('LEVEL_MJ', 3.0);
define('LEVEL_ADMIN', 4.0);

//@todo move that to JS
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

	'sign_off' => array('controller' => 'User', 'action' => 'delog'),
	'sign_in' => array('controller' => 'User', 'action' => 'login'),
	'vote' => array('controller' => 'User'),
	'credit' => array('controller' => 'User'),
	'vip' => array('controller' => 'Account'),
	'ladder_vote' => array('controller' => 'User'),

	'character.give' => array('controller' => 'Character', 'action' => 'give'),

	'join' => array('controller' => 'Misc'),
	'staff' => array('controller' => 'StaffRole', 'action' => 'index'),
	'stats' => array('controller' => 'Misc'),
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

set_include_path(implode(PATH_SEPARATOR, array(
	ROOT, //see #0.3.2a
	'lib/class/', //local libs > global libs
#		get_include_path(),
)));
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
//disable magic_quotes (horrible feature ...) ! @ = no error (magic_quotes: deprecated)
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
	//@todo remove that ? I guess I can just use $title vars

	$guildRights = array(2, 4, 8, 16, 32, 64, 128, 256, 512, 4092, 8192, 16384);


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
unset($login, $config['DB_HOST'], $config['DB_USER'], $config['DB_PSWD']);
if (DEBUG && !DEV)
	$mem .= memory_get_usage() . ': connections loaded - ' . __FILE__ . ':' . __LINE__ . '<br />';
$manager = Doctrine_Manager::getInstance();
$manager->setAttribute(Doctrine_Core::ATTR_QUERY_CLASS, 'Query');
$manager->setAttribute(Doctrine_Core::ATTR_TABLE_CLASS, 'RecordTable');
$manager->setAttribute(Doctrine_Core::ATTR_COLLECTION_CLASS, 'Collection');
$manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING,
		Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
$manager->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);
$manager->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, true);
unset($manager);
if (DEBUG && !DEV)
	$mem .= memory_get_usage() . ': Attributes loaded ... - ' . __FILE__ . ':' . __LINE__ . '<br />';

if (!DEV)
{
	load_models('other');

	session_start();

	//Shop item types
	$types = array(
#		ShopItemEffectTable::TYPE_ADD_PREFIX => lang('character.prefix_name'),
		-2 => lang('shop._add'),
		LiveActionTable::TYPE_LEVEL_UP => lang('shop.level_up'),
		LiveActionTable::TYPE_ADD_XP => lang('shop.xp'),
		LiveActionTable::TYPE_ADD_K => lang('shop.K'),
		LiveActionTable::TYPE_ADD_CAPITAL => lang('shop.capital'),
		LiveActionTable::TYPE_ADD_SPELLPOINT => lang('shop.spellpoint'),
		-3 => lang('shop._items'),
		LiveActionTable::TYPE_ITEM_JETS_ALEATOIRES => lang('shop.item_random'),
		LiveActionTable::TYPE_ITEM_JETS_MAX => lang('shop.item_perfect'),
		-4 => lang('shop._stats'),
		LiveActionTable::TYPE_CARAC_FORCE => lang('shop.stat.strength'),
		LiveActionTable::TYPE_CARAC_AGILITE => lang('shop.stat.agility'),
		LiveActionTable::TYPE_CARAC_CHANCE => lang('shop.stat.chance'),
		LiveActionTable::TYPE_CARAC_SAGESSE => lang('shop.stat.wisdom'),
		LiveActionTable::TYPE_CARAC_VITALITE => lang('shop.stat.vitality'),
		LiveActionTable::TYPE_CARAC_INTELLIGENCE => lang('shop.stat.intell'),
	);

	if (!empty($_SESSION['_csrf_token_req']) && $router->isPost())
	{ //disable _csrf_token if using edit in place (I currently have no other solution :<)
	 //the way I see that would be having a JS var _csrf_token which eIP would send WITH the actual form
	 //and this is what I'm gonna do when I'll have free time + motive to edit that script ...
		$requestToken = $router->postVar('_csrf_token');
		if ($requestToken !== session_id()) //using === here is VERY SIGNIFICANT
		{
			if (DEBUG)
				echo 'invalid token<hr />'; //simple notice ...
			else
				define('HTTP_CODE', 404);
		}
	}

	if (DEBUG)
		$mem .= memory_get_usage() . ': Models loaded ... - ' . __FILE__ . ':' . __LINE__ . '<br />';

	if (!empty($_SESSION['guid']))
	{ //retrieve account
		$accountQ = Query::create()
						->from('Account a')
							->leftJoin('a.Characters c INDEXBY guid')
								->leftJoin('c.Events e INDEXBY e.id')
							->leftJoin('a.User u')
								->leftJoin('u.PollOptions po')
									->leftJoin('po.Poll p')
								->leftJoin('u.Review r')
						->where('guid = ?', $_SESSION['guid']);
		$account = $accountQ->fetchOne();
		unset($accountQ);
#		exit('Memory used by ONE Query from Account WHERE guid = ? fetchOne WITHOUT ANY JOIN + Query object free\'d + unset\'d : ' . ( memory_get_usage() - $prev_mem));
#		for those asking : the result is 4 221 424 (1 try only, it's not an average)
		if ($account)
		{
			if (!$account->relatedExists('User'))
				$account->User = UserTable::getInstance()->fromGuid($account);
			if ($account->getMainChar())
				load_models('static');
		}
		else
			unset($_SESSION['guid']);

		if (DEBUG)
			$mem .= memory_get_usage() . ': Acc loaded ... - ' . __FILE__ . ':' . __LINE__ . '<br />';
	}
}
/* @var $account Account */
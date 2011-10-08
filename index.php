<?php
/**
 * InfiniteCMS
 *
 * @author Nami-Doc 100% of code !
 * @version $Id: index.php 56 2011-01-16 19:39:32Z nami.d0c.0 $
 *
 * This CMS has been created by Nami-D0C
 *
 * J'ai mis de nombreuses heures à coder ce CMS et son CMF, le résultat est bon à mes yeux,
 *  bien documenté, conçu agréablement (changement de template rapide, gestion multi-langues)
 *  et agréable à lire (fichiers assez concis sauf fonctions.php).
 * La plupart des CMS distribués sont sans boutique, mais je ne distribue que des choses
 *  fonctionnelles, pratiques, innovantes et surtout flexibles.
 *
 * Please respect my work
 * Veuillez respecter mon travail
 *
 *
 * Nami-D0C
 */
//$mem is used for my debug (shows where are memory leaks)
$mem = '<br />' . memory_get_usage() . ': Avant tout<br />';
define('ROOT', './');
define('EXT', strrchr(__FILE__, '.'));
define('DEV', false);
ini_set('include_path', implode(PATH_SEPARATOR, array(
		get_include_path(),
		ROOT . 'lib',
)));
$mem .= memory_get_usage() . ': Avant fonctions.php<br />';
//load API + bootstrap

//where should I put that kind of thing ?
if (!function_exists('get_called_class'))
	exit('Your PHP version seems too old. InfiniteCMS requires PHP5.3 at least.<br />
Votre version de PHP est trop vieille. InfiniteCMS a besoin d\'au minimum PHP5.3 pour fonctionner.');

try
{
	require 'lib/bootstrap' . EXT;
} catch (Exception $e)
{
	if (DEBUG)
		exit('problems with bootstrapping : '.$e->getMessage());
	else
		exit('Problems during the init. Please contact the server admin.');
}

$mem .= memory_get_usage() . ': Apres fonctions.php<br />';

$infos = $router->getInfos();

//Additional vars
$title = lang($infos['controller'] . ' - ' . $infos['action'], 'title', true);
$connected = $member->isConnected();
$isSpecialExt = $router->getExt() !== EXT && $router->getExt() !== NULL;
$output = (bool) $router->requestVar('output', 1);
$headers = $output ? ( !$isSpecialExt && $router->requestVar('header', 1) == 1 ) : false;
meta('Content-Type', 'text/html; charset=UTF-8'); //@todo move this
$mem .= memory_get_usage() . ': Avant controller+action<br />';
try
{
	ob_start();
	$file_include = str_replace(DS . $infos['action'], DS . '_include', $router->getPath());
	if (file_exists($file_include) && substr($file_include, -1) != DS)
		require_once $file_include;
	$error = tag('br') . tag('p', tag('h1', array('align' => 'center', 'style' => 'color: red;'), lang('error.404')));
	if ($connected ? !$account->banned : true)
	{ //we can access to the page (/actions/Module/_include.php) and the accunt is not banned
		if ((defined('HTTP_CODE') && HTTP_CODE != 200) || defined('LEVEL_FALLBACK')
			|| (( $router->getController() === NULL || $router->getAction() === NULL || $router->getExt() === NULL )
			 && !$router->isRoute())) //unfindable page
		{
			if (!defined('HTTP_CODE') && !defined('LEVEL_FALLBACK'))
				define('HTTP_CODE', 404);
		}
		else
		{ //normally load the page
			jQ(false, 'cache'); //enter cache mode
			require_once $router->getPath();
			jQ(false, 'main'); //go back to main mode
			jQ(jQ(NULL, 'cache')); //add remaining cache JS into main
		}
	}

	$connected = $member->isConnected(); //login action ,-)

	$data = ob_get_clean();
	if (defined('HTTP_CODE') && HTTP_CODE != 200) //code 200: ok
		$data = $error;
	$mem .= memory_get_usage() . ': Apres<br />';
	$erreurs = array();
	global $config, $connected;
	if ((defined('HTTP_CODE')
			&& ( HTTP_CODE == 404 && HTTP_CODE != 301 ))
		|| defined('LEVEL_FALLBACK')) #301 auth
		$title = sprintf('(%s)', lang('unknow'));

	if ($output)
	{
		if (!defined('HEADERS_SENT')) //self-defined headers
			header('Content-type: text/html; charset=UTF-8'); //UTF-8 is our MASTER §
		if ($headers)
		{
			$title = strtr(str_replace('{page}', $title, $config['TITLE']), array(
				'{server.name}' => $config['SERVER_NAME'],
				'{server.corp}' => $config['SERVER_CORP'],
			));
			if ($router->isAjax())
			{
				if ($router->requestVar('ajaxData', true))
					echo implode('<~>', array($title, getPath(), SERVER_STATE,
					 ($member->isConnected() ? $account->User->getNextPMNotif() : ''), defined('UPDATE_SELECTOR') ? UPDATE_SELECTOR : '', $data));
				else
					echo $data;

				if ($config['JAVASCRIPT'])
				{
					$jQ = jQ();
					if (!empty($jQ))
						echo js('
					jQuery( function( $ )
					{
						' . $jQ . '
					} );');
				}
			}
			else
			{
				$mem .= memory_get_usage() . ': Avant layout<br />';
				require 'tpl/layout' . EXT;
				$mem .= memory_get_usage() . ': Apres layout<br />';
			}
		}
		else
			echo $data;
	}
} catch (Exception $e)
{
	if (DEBUG)
		exit('Problems with page loading : ' . $e->getMessage() . '<br />' .
		 $e->getTraceAsString());
	else
		exit('Problems during page loading. Please contact the server admin.');
}
__shutdown();
if (DEBUG && !$router->isAjax() && $headers)
	echo $mem . memory_get_usage() . ': Fin<br />';
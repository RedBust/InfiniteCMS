<?php
if (false)#if (empty($config['URL_VOTE']) || $config['URL_VOTE'] == -1)
{
	define('HTTP_CODE', 404);
	return;
}

$usersDql = Query::create()
				->select('u.id, u.guid, u.votes, a.guid, a.pseudo, c.*')
				->from('User u')
					->innerJoin('u.Account a')
						->leftJoin('a.Characters c INDEXBY c.guid')
				->orderBy('u.votes DESC');
$pager = new Doctrine_Pager($usersDql, $router->requestVar('id', 1), 20); //$config['ARTICLES_BY_PAGE'] );
$ladder = $pager->execute();
$layout = new Doctrine_Pager_Layout($pager, new Doctrine_Pager_Range_Sliding(array('chunk' => 4)), to_url(array('controller' => $router->getController(), 'action' => $router->getAction(), 'id' => '')));
$layout->setTemplate('[<a href="{%url}">{%page}</a>]');
$layout->setSelectedTemplate('[<b>{%page}</b>]');

echo '
<table border="1" style="width: 100%">' . tag('tr', tag('td', tag('b', lang('pseudo')) . tag('td', tag('b', lang('votes')))
				. tag('td', tag('b', lang('character')))));
foreach ($ladder as $user)
{
	/* @var $p Character */
	if ($p = $user->Account->getMainChar())
	{
		$perso = tag('td', make_link($p));
	}
	else
	{
		$perso = tag('td', tag('i', lang('acc.ladder.no_character')));
	}
	$perso .= "\n";

	echo tag('tr', tag('td', make_link($user->Account)) . tag('td', $user['votes']) . $perso);
}
echo '
</table>';

echo tag('br'), echo paginate($layout);
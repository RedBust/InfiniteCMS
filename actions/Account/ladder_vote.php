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
						->leftJoin('a.Characters c')
				->orderBy('u.votes DESC');
$pager = new Doctrine_Pager($usersDql, $router->requestVar('id', 1), 20); //$config['ARTICLES_BY_PAGE'] );
$ladder = $pager->execute();
$layout = new Doctrine_Pager_Layout($pager, new Doctrine_Pager_Range_Sliding(array('chunk' => 4)), to_url(array('controller' => $router->getController(), 'action' => $router->getAction(), 'id' => '')) . '{%page_number}');
$layout->setTemplate('[<a href="{%url}">{%page}</a>]');
$layout->setSelectedTemplate('[<b>{%page}</b>]');

echo '
<table border="1" style="width: 100%">' . tag('tr', tag('td', tag('b', lang('pseudo')) . tag('td', tag('b', lang('votes')))
				. tag('td', tag('b', lang('character')))));
foreach ($ladder as $user)
{
	$p = null;
	foreach ($user->Account->Characters as $char)
	{ //find the highest perso lvl
		/* @var $char Character */
		if ($char['xp'] > ($p === NULL ? 0 : $p['xp']))
		{
			$p = $char;
		}
	}
	/* @var $p Character */
	if ($p === NULL)
	{ //$p is the "strongest" character (of this account - In fact, the strongest chracter is Chuck-Norris, on MY account §)
		$perso = tag('td', tag('i', lang('acc.ladder.no_character')));
	}
	else
	{
		$perso = tag('td', $p->getInfoLink());
	}
	$perso .= "\n";

	echo tag('tr', tag('td', $user->Account->getProfilLink()) . tag('td', $user['votes']) . $perso);
}
echo '
</table>';

echo tag('br');
if ($layout->getPager()->haveToPaginate())
	$affichage->display();
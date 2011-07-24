<?php
$page = $router->requestVar('id', 1);
$char = '';
$checkOn = 'xp';
if (null !== $char = $router->postVar('character'))
{
	$prev = Query::create()
				->select('COUNT(c.guid) AS prev')
					->from('Character c')
				->where('c.guid <= (SELECT sc.guid FROM Character sc WHERE sc.name = ?)', $char)
				->orderBy(sprintf('c.%s DESC', $checkOn));
	if (!$config['LADDER_ADMIN'])
	{
		$prev->leftJoin('c.Account a')
				->andWhere('a.level = 0');
	}
	$prev = $prev->fetchOneArray();
	$prev = $prev['prev'];
	if (0 == $prev)
	{
		$char = '';
		$page = 1;
		echo tag('span', array('style' => array('color' => 'red')), lang('character.does_not_exists')) . tag('br');
	}
	else
	{
		$page = ($prev - ($prev % $config['LADDER_LIMIT'])) / $config['LADDER_LIMIT'];
		++$page;
	}
}

$ladderDql = Query::create()
				->from('Character p')
					->leftJoin('p.Account a')
						->leftJoin('p.GuildMember gm')
							->leftJoin('gm.Guild g')
					->where('a.banned = 0');
if (!$config['LADDER_ADMIN'])
{
	$ladderDql->andWhere('a.level = 0');
}
$ladderDql->orderBy(sprintf('p.%s DESC', $checkOn));
$pager = new Doctrine_Pager($ladderDql, $page, $config['LADDER_LIMIT']);
$persos = $pager->execute();
/* @var $persos Collection */
$layout = new Doctrine_Pager_Layout($pager, new Doctrine_Pager_Range_Sliding(array('chunk' => 4)), to_url(array('controller' => $router->getController(), 'action' => $router->getAction(), 'id' => ''), false) . '{%page_number}');
$layout->setTemplate('[<a href="{%url}">{%page}</a>]');
$layout->setSelectedTemplate('[<b>{%page}</b>]');
if ($persos->count())
{
	echo tag('h1', lang('character.search')), make_form(array(
		array('character', lang('name'), NULL, $char),
	), APPEND_FORM_TAG), str_repeat(tag('br'), 3);
	$persos->ladderDisplay(($layout->getPager()->getPage() - 1) * $config['LADDER_LIMIT'], $char);
	if ($layout->getPager()->haveToPaginate())
		$layout->display();
}
else
	echo tag('b', lang('acc.ladder.no_character'));

<?php
$table = Doctrine_Core::getTable($router->getController());
/* @var $table GuildTable */
$col = $table->getIdentifier();
$c = -1;
foreach ($table->getColumnNames() as $col) //Guild/show/name/...
	if (( $c = $router->requestVar($col) ) !== NULL)
		break;
if (!$guild = $table->createQuery('g')
				->where('g.' . $col . ' = ?', $c)
					->leftJoin('g.Members gm')
						->leftJoin('gm.Character p')
				->orderBy('gm.rank ASC')
				->fetchOne())
{
	define('HTTP_CODE', 404);
	return;
}
/* @var $guild Guild */

$title = sprintf($title, $guild->name);

echo tag('h1', $guild->getName()),
 tag('b', lang('level') . ': '), $guild->lvl, tag('br'),
 tag('b', lang('xp') . ': '), number_format($guild->xp, 0, ',', ' '), tag('br'),
 tag('b', pluralize(lang('guild.nbr_members'), $guild->Members->count())) . ': ' . $guild->Members->count(), str_repeat(tag('br'), 3);

if ($guild->Members->count())
{
	$html = '';
	$gm_inTest = array();
	foreach ($guild->Members as $gm)
	{ /* @var $gm GuildMember */
		if ($gm->rank == 0)
			$gm_inTest[] = $gm;
		else
			$html .= $gm;
	}
	foreach ($gm_inTest as $gm)
		$html .= $gm;

	echo tag('b', lang('guild.mean_level')), $guild->getMeanLevel(), tag('br'),
	tag('table', array('style' => 'width: 100%;', 'border' => '1'),
		tag('thead', tag('tr', tag('th', tag('b', lang('guild.rank'))) .
			CharacterTable::getInstance()->getTableHeaderDatas(true) .
			tag('th', tag('b', lang('guild.xp_given'))) .
			tag('th', tag('b', lang('guild.xp_per'))))) .
		tag('tbody', array('style' => $guild->Members->count() > 20 ? array('height' => '550px', 'overflow' => 'auto') : array()), $html));
		
}
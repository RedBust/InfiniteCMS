<?php
if (!$config['STATS'])
{
	define('HTTP_CODE', 404);
	return;
}
define('SKIP_STATS', true);

if ($cache = Cache::start($router->getController() . '_stats', 1))//'+6 hours'))
{
	$characters = Query::create()
						->from('Character c')
							->leftJoin('c.GuildMember gm')
						->fetchArray();
	$accounts = Query::create()
					->from('Account')
					->fetchArray();
	$guilds = Query::create()
					->from('Guild')
					->fetchArray();
	$items = Query::create()
					->select('COUNT(*) as size')
						->from('Item')
					->fetchOneArray();
	

	$counts = array(
		'characters' => count($characters),
		'accounts' => count($accounts),
		'guilds' => count($guilds),
		'items' => $items['size'],
		'guilded' => 0,

		'breed' => array(),
		'gender' => array(),

		'cLevel' => 0,
		'gLevel' => 0,

		'kamas' => 0,
	);
	echo tag('ul', tag('li', tag('b', number_format($counts['accounts'], 0, '.', ' ')) . ' ' . pluralize(lang('acc'), $counts['accounts']) . '.') .
		tag('li', tag('b', number_format($counts['characters'], 0, '.', ' ')) . ' ' . pluralize(lang('character'), $counts['characters']) . '.') .
		tag('li', tag('b', number_format($counts['guilds'], 0, '.', ' ')) . ' ' . pluralize(lang('acc.ladder.guild'), $counts['guilds']) . '.') .
		tag('li', tag('b', number_format($counts['items'], 0, '.', ' ')) . ' ' . pluralize(ucfirst(lang('character.item')), $counts['items']) . '.'));
		//avg number of char / accounts : $counts['characters'] / $counts['accounts'] ... so hard ...

	if ($counts['characters'])
	{
		foreach ($characters as &$character)
		{
			if (isset($counts['breed'][$character['class']]))
				++$counts['breed'][$character['class']];
			else
				$counts['breed'][$character['class']] = 1;

			if (isset($counts['gender'][$character['sexe']]))
				++$counts['gender'][$character['sexe']];
			else
				$counts['gender'][$character['sexe']] = 1;

			$counts['cLevel'] += $character['level'];
			$counts['kamas'] += $character['kamas'];

			if (isset($character['GuildMember']))
				++$counts['guilded'];

			unset($character);
		}
		foreach ($guilds as &$guild)
		{
			$counts['gLevel'] += $guild['lvl'];
			unset($guild);
		}
		$counts['avgCLevel'] = $counts['cLevel'] / $counts['characters'];
		if ($counts['guilds'])
			$counts['avgGLevel'] = $counts['gLevel'] / $counts['guilds'];
		$counts['avgKamas'] = $counts['kamas'] / $counts['characters'];
		echo tag('ul', tag('li', tag('b', lang('character.avg_level')) . ': ' . number_format($counts['avgCLevel'], 0, '.', ' ')) .
		 tag('li', tag('b', lang('character.avg_kamas')) . ': ' . number_format($counts['avgKamas'], 0, '.', ' ')) .
		 tag('li', tag('b', pluralize(lang('character.guilded'), $counts['guilded'], false)) . ': ' . number_format($counts['guilded'], 0, '.', ' ')) .
		 ( $counts['guilds'] ? tag('li', tag('b', lang('guild.avg_level')) . ': ' . number_format($counts['avgGLevel'], 0, '.', ' ')) : '' ));

		if (count($counts['gender']) > 1) //having such helpers is soo cool
			echo chart(pluralize(lang('acc.ladder.sex'), 2, false), $counts['gender'], 'gender_.');
		if (count($counts['breed']) > 1)
			echo chart(pluralize(lang('acc.ladder.class'), 2, false), $counts['breed'], 'breed.');
	}
}
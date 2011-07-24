<?php
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
		tag('li', tag('b', number_format($counts['guilds'], 0, '.', ' ')) . ' ' . pluralize(lang('acc.ladder.guild'), $counts['characters']) . '.') .
		tag('li', tag('b', number_format($counts['items'], 0, '.', ' ')) . ' ' . pluralize(ucfirst(lang('character.item')), $counts['items']) . '.'));

	if ($counts['characters'])
	{
		foreach ($characters as $character)
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
		}
		foreach ($guilds as $guild)
		{
			$counts['gLevel'] += $guild['lvl'];
		}
		$counts['avgCLevel'] = $counts['cLevel'] / $counts['characters'];
		$counts['avgGLevel'] = $counts['gLevel'] / $counts['guilds'];
		$counts['avgKamas'] = $counts['kamas'] / $counts['characters'];
		echo tag('ul', tag('li', tag('b', lang('character.avg_level')) . ': ' . $counts['avgCLevel']) .
		 tag('li', tag('b', lang('character.avg_kamas')) . ': ' . number_format($counts['avgKamas'], 0, '.', ' ')) .
		 tag('li', tag('b', pluralize(lang('character.guilded'), $counts['guilded'], false)) . ': ' . number_format($counts['guilded'], 0, '.', ' ')) .
		 tag('li', tag('b', lang('guild.avg_level')) . ': ' . $counts['avgGLevel'])) .
			tag('div', array('id' => 'highGender'), '') . tag('div', array('id' => 'highBreed'), '');
		if (count($counts['gender']) > 1)
		{
			$js_genders = array();
			foreach ($counts['gender'] as $gender => $count)
			{
				$js_genders[] = '["' . lang('gender_.' . $gender) . '", ' . round(($count * 100) / $counts['characters'], 2) . ']';
			}
			$js_genders = implode(', ', $js_genders);
			jQ("
	chart = new Highcharts.Chart({
		  chart: {
			 renderTo: 'highGender',
			 plotBackgroundColor: null,
			 plotBorderWidth: null,
			 plotShadow: false
		  },
		  title: {
			 text: '" . pluralize(lang('acc.ladder.sex'), 2, false) . "'
		  },
		  tooltip: {
			 formatter: function() {
				return '<b>'+ this.point.name +'</b>: '+ this.y +' %';
			 }
		  },
		  plotOptions: {
			 pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
				   enabled: true
				},
				showInLegend: true
			 }
		  },
		   series: [{
			 type: 'pie',
			 data: [
				{$js_genders}
			 ]
		  }]
	   });");
		} //end count.genders
		if (count($counts['breed']) > 1)
		{
			$js_breeds = array();
			foreach ($counts['breed'] as $breed => $count)
			{
				$js_breeds[] = '["' . lang('breed.' . $breed) . '", ' . round(($count * 100) / $counts['characters'], 2) . ']';
			}
			$js_breeds = implode(', ', $js_breeds);
			jQ("
	chart = new Highcharts.Chart({
		  chart: {
			 renderTo: 'highBreed',
			 plotBackgroundColor: null,
			 plotBorderWidth: null,
			 plotShadow: false
		  },
		  title: {
			 text: '" . pluralize(lang('acc.ladder.class'), 2, false) . "'
		  },
		  tooltip: {
			 formatter: function() {
				return '<b>'+ this.point.name +'</b>: '+ this.y +' %';
			 }
		  },
		  plotOptions: {
			 pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
				   enabled: true
				},
				showInLegend: true
			 }
		  },
		   series: [{
			 type: 'pie',
			 data: [
				{$js_breeds}
			 ]
		  }]
	   });");
		}
	}
}
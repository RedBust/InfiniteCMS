<?php
if (!check_level(LEVEL_MODO))
	return;

if (empty($_POST))
{
	echo tag('h1', lang('admin.characters.search')) .
	make_form(array(
		array('name', lang('name') . lang('joker') . tag('br')),
	));
}
else
{
	echo tag('h1', lang('results'));
	$characters = Query::create()
					->from('Character p')
						->where('name LIKE ?', str_replace('*', '%', $router->postVar('name')))
					->limit(10) //yeah, DYPY (Do your paginator yourself). I may consider having might for maybe do that ... But I don't think so. My beliefs told me to not touch that !
					->execute();
	if ($characters->count())
	{
		echo '<ul>';
		foreach ($characters as $char)
		{
			/* @var $char Character */
			echo tag('li', $char->getInfoBox());
		}
		echo '</ul>';

		/* */
	}
	else
		echo tag('h3', lang('no_result'));
}
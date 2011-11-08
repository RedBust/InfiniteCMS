<?php
$contests = Query::create()
				->from('Contest c')
				->execute();

echo tag('h1', $title);

if ($contests->count())
{
	echo '<ul>';
	foreach ($contests as $contest)
	{
		echo tag('li', ( $contest->ended ? '(' . lang('ended') . ')' : '' ) . make_link($contest));
	}
	echo '</ul>';
}
else
	echo lang('contest.any');

if (level(LEVEL_ADMIN))
	echo tag('br') . tag('br') . make_link(new Contest);
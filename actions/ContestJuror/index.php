<?php
$levels = Member::getFormattedLevels(true);
echo '<fieldset>', tag('legend', pluralize(lang('contest.juror'), 2)),
 sprintf(lang('acc_w/_lv>='), $levels[$contest->level]);

if ($contest->Jurors->count())
{
	echo '<ul>';
	foreach ($contest->Jurors as $juror)
	{
		echo tag('li', make_link($juror->Account));
	}
	echo '</ul>';
}
else
	echo tag('br');

if (level(LEVEL_ADMIN) && !$contest->ended && $contest->level != LEVEL_LOGGED)
	echo make_link(array('controller' => 'ContestJuror', 'action' => 'update', 'contest' => $contest->id), lang('ContestJuror - create', 'title'));
echo '</fieldset>';
<?php
$levels = Member::getFormattedLevels(true);
echo sprintf(lang('contest.acc_must_lv<'), $levels[$contest->level]),
 make_form(array(	
	array('pseudo', lang('name'), NULL),
	array('contest', NULL, 'hidden', $contest->id),
));
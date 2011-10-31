<?php
echo make_form(array(
	array('name', lang('name') . tag('br'), NULL, $contest->name),
	array('level', lang('contest.level') . tag('br'), 'select', Member::getLevels(true), $contest->level),
	array('reward', tag('br') . lang('reward') . tag('br'), 'record', array('type' => 'one', 'model' => 'Items', 'empty' => true, 'parent' => 'ShopCategory', 'pColumn' => 'name'), $contest->reward_id)
));
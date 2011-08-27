<?php
$nl = tag('br');
echo make_form(array(
	lang('user.points_stats') => array(
		array('points', lang('user.points') . $nl, NULL, $c->points),
		array('audiotel', $nl . $nl . lang('user.pass') . $nl, NULL, $c->audiotel),
		array('votes', $nl . $nl . lang('user.vote') . $nl, NULL, $c->votes),
	),
), APPEND_FORM_TAG);
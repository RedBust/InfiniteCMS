<?php
echo make_form(array(
	array('name', lang('name') . tag('br'), NULL, $event->name),
	array('period', tag('br') . lang('datetime') . tag('br'), 'datetime', $event->period, array('__restrict' => '@today+;')),
	( $event->exists() ? array('id', NULL, 'hidden', $event->id) : NULL ),
), APPEND_FORM_TAG);
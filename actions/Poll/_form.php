<?php
echo make_form(array(
	array('name', lang('name') . tag('br'), NULL, $poll->getRawName()),
	array(array('date_start', 'date_end'), array(tag('br') . lang('poll.date_start') . tag('br'), tag('br') . lang('poll.date_end') . tag('br')), 'date_range', array($poll->date_start, $poll->date_end)),
));
<?php
echo make_form(array(
	array('name', lang('name') . tag('br'), NULL, $role->name),
	array('account', NULL, 'hidden', $role->account_id),
));
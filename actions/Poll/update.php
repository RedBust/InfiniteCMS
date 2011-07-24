<?php
if (!check_level(LEVEL_ADMIN))
	return;

if (!( $poll = PollTable::getInstance()->find($id = $router->requestVar('id')) ))
	$poll = new Poll;

if (!empty($_POST))
{
	$col = $router->requestVar('col', '');
	$vals = $_POST;
	if (!$poll->getTable()->hasColumn($col) || empty($_POST['update_value']))
		$col = array();
	else //replace $vals
		$vals = array($col => $_POST['update_value']);
	$errors = $poll->maj($vals, $col);	
	if (!empty($col) && !is_array($col))
	{
		$val = $poll[$col];
		if (substr($col, 0, 4) == 'date')
			$val = date_to_picker($val);
		exit(nl2br($val));
	}
}
if (empty($_POST) || $errors != array())
{
	partial('_form', array('poll'), PARTIAL_CONTROLLER);
}
elseif (!empty($_POST) && $errors == array() && $headers)
{
	echo lang('poll.saved') . make_link('@root', lang('back_to_index'));
	redirect(array('controller' => $router->getController(), 'action' => 'show', 'id' => $poll['id']));
}
<?php
if (!check_level(LEVEL_ADMIN))
	return;

if (!$poll = PollTable::getInstance()->find($id = $router->requestVar('id')))
	$poll = new Poll;

if (!empty($_POST))
{
	$col = $router->requestVar('col', '');
	$vals = $_POST;
	if (!$poll->getTable()->hasColumn($col) || empty($_POST['update_value']))
		$col = array();
	else //replace $vals
		$vals = array($col => $_POST['update_value']);
	$errors = $poll->update_attributes($vals, $col);
	if (!empty($col) && !is_array($col))
	{
		$val = $poll[$col];
		if (substr($col, 0, 4) == 'date')
			$val = date_to_picker($val);
		exit(nl2br($val));
	}
}
if (empty($_POST) || !empty($errors))
	partial('_form', array('poll'), PARTIAL_CONTROLLER);
else if (!empty($_POST) && empty($errors) && $headers)
	redirect(array('controller' => $router->getController(), 'action' => 'show', 'id' => $poll['id']));
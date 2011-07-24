<?php
if (!check_level(LEVEL_ADMIN))
	return;

if (!( $poll = PollTable::getInstance()->find($id = $router->requestVar('id', -1)) ))
{
	printf(lang('poll.not_exists'), html($id));
	return;
}

if (!empty($_POST))
{
	$col = $router->requestVar('col', '');
	$poll_option = new PollOption;
	$poll_option->Poll = $poll;
	if (!empty($_POST['name']))
	{
		$poll_option->name = $_POST['name'];
		$poll_option->save();
	}
	else
	{
		$errors['name'] = sprintf(lang('must_!empty'), lang('name'));
	}
}
if (empty($_POST) || $errors != array())
{
	printf(lang('poll.option.new_for'), $poll->name);
	partial('_form', array('poll_option'), PARTIAL_CONTROLLER);
}
elseif (!empty($_POST) && $errors == array() && $headers)
{
	echo lang('poll.option.saved') . make_link('@root', lang('back_to_index'));
	redirect(array('controller' => 'Poll', 'action' => 'show', 'id' => $poll['id']));
}
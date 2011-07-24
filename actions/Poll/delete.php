<?php
if (!check_level(LEVEL_ADMIN))
	return;

$poll = PollTable::getInstance()->find($id = $router->requestVar('id'));
if (!$poll)
{
	printf(lang('poll.not_exists'), html($id));
	return;
}
$poll->delete();
redirect('@polls');
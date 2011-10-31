<?php
if (!check_level(LEVEL_ADMIN))
	return;

$router->codeUnless(404, $poll = PollTable::getInstance()->find($id = $router->requestVar('id')));
$poll->delete();
redirect('@polls');
<?php
if (!( $pollOption = PollOptionTable::getInstance()->find($id = $router->requestVar('id', -1)) ))
{
	return;
}
$pollID = $pollOption->poll_id;
$pollOption->delete();
redirect(array('controller' => 'Poll', 'action' => 'show', 'id' => $pollID));
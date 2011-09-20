<?php
if (!check_level(LEVEL_LOGGED))
	return;
if (!( $poll_option = Query::create()
							->from('PollOption po')
								->leftJoin('po.Poll p')
							->where('po.id = ?', $id = $router->requestVar('id', -1))
							->fetchOne() ))
{
	echo lang('poll.option.not_exists');
	return;
}
if ($poll_option->Poll->isElapsed())
{
	define('HTTP_CODE', 404);
	return;
}
if (!$account->User->canVote($poll_option->Poll))
{
	echo lang('poll.option.cannot_vote');
	return;
}

$poll_account = new PollOptionUser;
$poll_account->PollOption = $poll_option;
$poll_account->User = $account->User;
$poll_account->save();
redirect(array('controller' => 'Poll', 'action' => 'show', 'id' => $poll_option->Poll->id));
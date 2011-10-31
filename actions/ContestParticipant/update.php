<?php
if (!check_level(LEVEL_LOGGED))
	return;

$router->codeUnless(404, $contest = ContestTable::getInstance()->find($id = $router->requestVar('id')));
if ($contest->ended)
{
	echo lang('contest.ended');
	return;
}
if (!$account->canCompete($contest))
{
	echo lang('contest.cant_c');
	return;
}

$participant = new ContestParticipant;
$participant->Contest = $contest;
$participant->Character = $account->getMainChar();
$participant->save();
redirect($contest->getUrl() + array('character' => $account->getMainChar()->name));
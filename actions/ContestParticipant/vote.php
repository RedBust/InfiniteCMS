<?php
if (!check_level(LEVEL_LOGGED))
	return;

$router->codeUnless(404, $contest = ContestTable::getInstance()->retrieve());
$router->codeUnless(404, $participant = $contest->Participants[$router->requestVar('id')]);
$router->codeUnless(404, $account->canJudge($contest));
$participant->votes += 1;
$voter = new ContestVoter; //yeah just $contest->Voters[] = $account->getUser(); won't work
$voter->user_id = $account->getUser()->id;
$voter->contest_id = $contest->id;
$voter->save();
$contest->save();
redirect($contest);
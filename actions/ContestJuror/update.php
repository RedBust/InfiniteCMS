<?php
if (!check_level(LEVEL_ADMIN))
	return;

$juror = new ContestJuror;
$title = lang($router->getController() . ' - create', 'title');

$router->codeUnless(404, $contest = ContestTable::getInstance()->retrieve());
$router->codeIf(404, $contest->level == LEVEL_LOGGED);

if (count($_POST))
{
	$juror->contest_id = $contest->id;

	if (empty($_POST['pseudo']) ? true : !($acc = AccountTable::getInstance()->findOneByPseudo($_POST['pseudo'])))
		$errors[] = sprintf(lang('must_!empty'), 'pseudo');
	else
	{
		if ($acc->level >= $contest->level)
			$errors[] = lang('contest.acc_lv>c_lv');
		else if ($contest->Jurors->contains($acc->getUser()->id))
			$errors[] = lang('contest.already_juror');
		else
			$juror->user_id = $acc->getUser()->id;
	}

	if (empty($errors))
		$juror->save();
}
if (empty($_POST) || count($errors))
	partial('_form', 'contest', PARTIAL_CONTROLLER);
else
	redirect($contest);
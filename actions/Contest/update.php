<?php
if (!check_level(LEVEL_ADMIN))
	return;

$table = ContestTable::getInstance();
if (!$contest = $table->retrieve())
{ //allow editing ?
	$contest = new Contest;
	$title = lang($router->getController() . ' - create', 'title');
}

if (!empty($_POST))
{
	if (empty($_POST['name']))
		$errors['name'] = sprintf(lang('must_!empty'), 'name');
	else
		$contest->name = $_POST['name'];

	$reward = ShopItemTable::getInstance()->find($_POST['reward']);
	if ($reward)
		$contest->Reward = $reward;
	else
		$errors['reward'] = lang('shop.does_not_exists');

	if (array_key_exists($router->postVar('level'), Member::getLevels(true)))
		$contest->level = $_POST['level'];
	else
		$errors['level'] = sprintf(lang('must_!empty'), 'level');


	if (empty($errors))
		$contest->save();
}
if (count($_POST) < 1 || $errors != array())
	partial('_form', array('contest'), PARTIAL_CONTROLLER);
elseif (count($_POST) > 0 && $errors == array())
	redirect($contest);
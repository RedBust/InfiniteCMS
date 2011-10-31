<?php
if (!check_level(LEVEL_ADMIN))
	return;

$id = $router->requestVar('id', $_SESSION['guid']);
$router->codeUnless($acc = AccountTable::getInstance()
					->createQuery('a')
						->leftJoin('a.User u')
					->where('a.guid = ?', $id) //guid => account id
					->fetchOne());
$c = $acc->getUser();
/* @var $c User */
if (!empty($_POST))
{
	$cols = $router->requestVar('col', '');
	$vals = $_POST;
	if ($c->getTable()->hasColumn($cols) && isset($_POST['update_value']))
		$vals = array($cols => $_POST['update_value']);
	else
		$cols = array('points', 'votes', 'audiotel');

	foreach ((array) $cols as $i => $col)
	{
		if (!empty($vals[$col])) //ok I use isset() because of "0" value (level: Player, points: 0, ...)
			$c->$col = intval($vals[$col]);
		else
			$errors[$col] = sprintf(lang('must_!empty'), $col);
	}
	$c->save();
	if (!empty($cols) && !is_array($cols))
		exit(nl2br($c[$col]));
}
if (empty($_POST) || !empty($errors))
	partial('_form', 'c', PARTIAL_CONTROLLER);
else
	redirect($acc);
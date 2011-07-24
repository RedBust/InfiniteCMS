<?php
//edit "User"
if (!check_level(LEVEL_ADMIN))
	return;

$id = $router->requestVar('id', $_SESSION['guid']);
$acc = AccountTable::getInstance()
					->createQuery('a')
						->leftJoin('a.User u')
					->where('a.guid = ?', $id) //guid => account id
					->fetchOne();		
if (!$acc)
{
	echo lang('acc.does_not_exists');
	return;
}
if (!$acc->relatedExists('User'))
	$acc->User = UserTable::getInstance()->fromGuid($acc->guid);
$c = $acc->User;
/* @var $c User */
$sent = count($_POST) > 0;
if ($sent)
{
	$cols = $router->requestVar('col', '');
	$vals = $_POST;
	if ($c->getTable()->hasColumn($cols) && isset($_POST['update_value']))
		$vals = array($cols => $_POST['update_value']);
	else
		$cols = array('points', 'votes', 'audiotel');

	foreach ((array) $cols as $i => $col)
	{
		if ($col == 'points')
			$vals[$col] = intval($vals[$col]);
#		if ($col == 'level' && !in_array(Member::getLevels()))
#		{
#			$errors[$col] = sprintf();
#			continue;
#		}
		if (isset($vals[$col])) //ok I use isset() because of "0" value (level: Player, points: 0, ...)
			$c->$col = $vals[$col];
		else
			$errors[$col] = sprintf(lang('must_!empty'), $col);
	}
	$c->save();
	if (!empty($cols) && !is_array($cols))
		exit(nl2br($c[$col]));
}
if (!$sent || $errors != array())
{
	partial('_form_user', 'c', PARTIAL_CONTROLLER);
}
elseif ($sent && $errors === array())
{
	echo lang('acc.edited');
	redirect(array(
		'controller' => $router->getController(),
		'action' => 'show',
		'id' => $acc->guid,
	));
}
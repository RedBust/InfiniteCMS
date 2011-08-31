<?php
if (!level(LEVEL_LOGGED))
	redirect(array('controller' => $router->getController(), 'action' => 'create'));

if (level(LEVEL_ADMIN) && !empty($_SESSION['referer']) && $_SESSION['referer'] === 'list')
{
	unset($_SESSION['referer']);
	define('FROM_SEARCH', true);
}
else
	define('FROM_SEARCH', false);

$sent = count($_POST) > 0;
$req_id = NULL; //request_id
if (level(LEVEL_ADMIN) && ( $req_id = $router->requestVar('id') ) !== NULL && $req_id !== $_SESSION['guid'])
	$acc = AccountTable::getInstance()->find($req_id);
else
	$acc = $account;
/* @var $acc Account */
if (!$acc)
{
	if ($req_id !== NULL)
		echo lang('acc.does_not_exists');
	else
		define('HTTP_CODE', 404);
	return;
}

if ($sent)
{
	$col = $router->requestVar('col', array());
	if ($col !== array() && !in_array($col, $acc->getColumns()) && !check_level(LEVEL_ADMIN))
		return; //the column does not exists?
	$vals = $_POST;
	if (!in_array($col, $acc->getColumns()) || !isset($_POST['update_value']))
		$col = true; //update all
	else
		$vals = array($col => $_POST['update_value']); //only a col
	$errors = $acc->update_attributes($vals, $col);

	if (!empty($col) && !is_array($col) && $col !== true)
	{
		$val = nl2br($acc[$col]);
		if ($col === 'level')
			$val = $acc->getLevel();
		exit($val);
	}
}
if (!$sent || $errors != array())
{
	partial('_form', 'acc', PARTIAL_CONTROLLER);
}
elseif ($sent && $errors === array())
{
	echo lang('acc.edited');
	redirect(array('router' => $router->getController(), 'action' => 'show', 'id' => $acc->guid));
}
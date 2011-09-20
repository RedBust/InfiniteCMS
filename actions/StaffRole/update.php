<?php
if (!check_level(LEVEL_ADMIN))
	return;

if (!$role = StaffRoleTable::getInstance()->find($id = $router->requestVar('id')))
{
	$role = new StaffRole;
	$title = lang($router->getController() . ' - create', 'title');
}

if (!empty($_REQUEST['account']))
{
	$account_id = $_REQUEST['account'];
	if ($acc = AccountTable::getInstance()->find($account_id))
		$role->Account = $acc;
	else
		$errors[] = sprintf(lang('must_!empty'), 'account');
}

if ($sent = ( count($_POST) > 0 ))
{
	if (!empty($_POST['name']))
		$role->name = $_POST['name'];
	else
		$errors[] = sprintf(lang('must_!empty'), 'name');

	if (empty($errors))
		$role->save();
}
if (!$sent || $errors != array())
	partial('_form', array('role'), PARTIAL_CONTROLLER);
elseif ($sent && $errors === array())
	redirect(array('controller' => 'StaffRole'));
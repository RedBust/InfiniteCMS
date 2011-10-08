<?php
if (!check_level(LEVEL_GUEST)) //yeah, level guest is required. SO WAT ?
	return;
if (!$config['ENABLE_REG'])
{
	echo tag('b', lang('acc.register_disabled'));
	return;
}

$acc = new Account;
if (count($_POST))
	$errors = $acc->update_attributes($_POST, true);
if (!count($_POST) || $errors != array())
{
	if (!$config['ALLOW_MULTI'] && $acc->getTable()->findOneByLastip(ip2long($member->getIp())))
	{
		echo lang('acc.register.error.already_created_acc');
		return;
	}
	partial('_form', 'acc', PARTIAL_CONTROLLER);
}
else if (count($_POST) && $errors === array())
	echo lang('acc.account_created');
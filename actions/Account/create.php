<?php
$sent = count($_POST);
$acc = new Account;

if (!check_level(LEVEL_GUEST)) //yeah, level guest is required. SO WAT ?
	return;
if (!$config['ENABLE_REG'])
{
	echo tag('b', lang('acc.register_disabled'));
	return;
}

if ($sent)
	$errors = $acc->update_attributes($_POST, true);
if (!$sent || $errors != array())
{
	if (!$acc->exists() && !$config['ALLOW_MULTI']
			&& $acc->getTable()->findOneByLastip(ip2long($member->getIp())))
	{
		echo lang('acc.register.error.already_created_acc');
		return;
	}
	partial('_form', 'acc', PARTIAL_CONTROLLER);
}
else if ($sent && $errors === array())
	echo lang('acc.account_created');
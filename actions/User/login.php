<?php
$link_back = make_link('@root', lang('back_to_index'));
$check = (bool) $router->requestVar('check', 0);

if (level(LEVEL_LOGGED))
{
	echo lang('acc.already_connected') . $link_back;
	return;
}
$pseudo = $router->requestVar(Member::CHAMP_PSEUDO);
$pass = $router->requestVar(Member::CHAMP_PASS);
if (empty($pseudo) || empty($pass))
	return; // no error :p
if (!( $account = AccountTable::getInstance()
							->createQuery('a')
								->leftJoin('a.Characters c INDEXBY guid')
								->leftJoin('a.User u')
							->where('account = ? AND pass = ?', array($pseudo, $pass))
							->fetchOne() ))
{
	if ($check)
		exit('bad');
	else
		echo lang('acc.invalid_login_action') . $link_back;
	return;
}
/* @var $account Account */
if ($account->banned)
{
	if ($check)
		exit('ban');
	else
		echo lang('acc.banned');
	return;
}
if ($check)
	exit('ok');

if (!$account->relatedExists('User'))
	$account->User = UserTable::getInstance()->fromGuid($account);
$_SESSION['guid'] = $account->guid;

echo lang('acc.now_connected') . $link_back;
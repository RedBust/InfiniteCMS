<?php
if (!check_level(LEVEL_LOGGED))
	return;
if ($account->Characters->count() < 2)
{
	echo lang('character.main.req_>1');
	return;
}

$char = intval($router->requestVar('id'));
if ($char === 0)
	echo $account->getCharactersList(true, $account->getMainChar() ? $account->getMainChar()->guid : array(), array('controller' => $router->getController(), 'action' => $router->getAction(), 'id' => ''));
else
{
	if (!$account->Characters->contains($char))
	{
		echo lang('character.!on_acc');
		return;
	}

	$account->User->main_char = $char;
	echo lang('character.main.modified');
}
<?php
if (!check_level(LEVEL_LOGGED))
	return;

if (!$account->Characters->count())
{
	echo lang('character.main.lempty');
	return;
}
if ($charID = $router->requestVar('id'))
{
	if ($account->Characters->has($charID))
	{
		$account->User->main_char = $charID;
		redirect('@root');
	}
	else
		echo lang('character.!on_acc');
}
else
{
	echo $account->getCharactersList();
}
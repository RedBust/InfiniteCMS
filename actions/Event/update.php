<?php
if (!check_level(LEVEL_LOGGED))
	return;

if ($event = EventTable::getInstance()->find($id = $router->requestVar('id')))
{	
	if (!check_level(LEVEL_ADMIN))
		return;
	if ($event->isElapsed())
	{
		echo lang('event.elapsed');
		return;
	}
	$event->refreshDatePassed(); //for teh next time.
}
else
	$event = new Event;

if (( $mainChar = $account->getMainChar() ) && $mainChar->isGM())
	$guildId = $mainChar->GuildMember->guild;
else if (level(LEVEL_ADMIN))
	$guildId = -1;
else
{
	define('HTTP_CODE', 404);
	return;
}

if (!empty($_POST))
{
	if (!empty($_POST['name']))
		$event->name = level(LEVEL_ADMIN) ? $_POST['name'] : html($_POST['name']);
	if (!empty($_POST['name']) && $period = datetime_from_picker($_POST['period']))
		$event->period = $period->format('Y-m-d H:i:00');
	if (isset($_POST['capacity']))
	{
		if (is_numeric($_POST['capacity']))
			$event->capacity = $_POST['capacity'];
		else
			$errors['capacity'] = sprintf(lang('must_numeric'), lang('capacity'));
	}

	if ($guildId != -1 && level(LEVEL_ADMIN) && empty($_POST['is_guild']))
		$guildId = -1;
	if ($guildId != -1)
		$event->guild_id = $guildId;
	else if (level(LEVEL_ADMIN) && !empty($_POST['reward']) && $_POST['reward'] != -1 && $event->capacity != 0)
	{
		$reward = ShopItemTable::getInstance()->find($_POST['reward']);
		if ($reward)
			$event->Reward = $reward;
		else
			$errors['reward'] = lang('shop.does_not_exists');

		if (isset($_POST['is_tombola']))
			$event->is_tombola = $_POST['is_tombola'] == 'on';
	}

	if (empty($event->name))
		$errors[] = sprintf(lang('must_!empty'), 'name');
	if (empty($event->period))
		$errors[] = sprintf(lang('must_!empty'), 'period');
	else if ($event->isPeriodPassed())
		$errors[] = lang('event.elapsed');

	if (empty($errors))
		$event->save();
}
if (empty($_POST) || $errors != array())
{
	partial('_form', array('event', 'guildId'), PARTIAL_CONTROLLER);
}
else if (!empty($_POST) && $errors == array() && $headers)
{
	redirect(array('controller' => $router->getController(), 'action' => 'index',
	 'year' => substr($event->period, 0, 4), 'month' => substr($event->period, 5, 2)));
}
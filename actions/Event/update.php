<?php
if (!check_level(LEVEL_ADMIN))
	return;

if ($event = EventTable::getInstance()->find($id = $router->requestVar('id')))
{
	if ($event->isElapsed())
	{
		echo lang('event.elapsed');
		return;
	}
	$event->refreshElapsed(); //for teh next time.
}
else
	$event = new Event;

if (!empty($_POST))
{
	if (!empty($_POST['name']))
		$event->name = $_POST['name'];
	if (!empty($_POST['name']) && $period = datetime_from_picker($_POST['period']))
		$event->period = $period->format('Y-m-d H:i:00');

	if (empty($event->name))
		$errors[] = sprintf(lang('must_!empty'), 'name');
	if (empty($event->period))
		$errors[] = sprintf(lang('must_!empty'), 'period');
	else if ($event->isElapsed())
		$errors[] = lang('event.elapsed');

	if (empty($errors))
		$event->save();
}
if (empty($_POST) || $errors != array())
{
	partial('_form', array('event'), PARTIAL_CONTROLLER);
}
else if (!empty($_POST) && $errors == array() && $headers)
{
	redirect(array('controller' => $router->getController(), 'action' => 'index',
	 'year' => substr($event->period, 0, 4), 'month' => substr($event->period, 5, 2)));
}
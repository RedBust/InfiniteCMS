<?php
if (!check_level(LEVEL_LOGGED))
	return;
if ($account->canParticipate($id = intval($router->requestVar('id', -1))))
{
	echo lang('event.not_participating');
	return;
}

if (!$event = EventTable::getInstance()->find($id))
{
	echo lang('event.does_not_exist');
	return;	
}
if ($event->isElapsed())
{
	echo lang('event.elapsed');
	return;
}

foreach ($account->Characters as $char)
	if ($char->Events->contains($event->id))
		break;
$event->unlink('Participants', array($char->guid));
$event->save();

redirect($event->getURL());
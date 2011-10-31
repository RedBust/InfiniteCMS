<?php
if (!check_level(LEVEL_LOGGED))
	return;
if ($account->canParticipate($id = intval($router->requestVar('id', -1))))
{
	echo lang('event.not_participating');
	return;
}

$router->codeUnless(404, $event = EventTable::getInstance()->find($id));
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
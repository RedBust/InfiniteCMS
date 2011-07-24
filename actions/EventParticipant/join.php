<?php
if (!check_level(LEVEL_LOGGED))
	return;
if (!$account->canParticipate($id = intval($router->requestVar('id', -1))))
{
	echo lang('event.cant_participate');
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

$char = $router->requestVar('char', -1);
if ($account->Characters->contains($char))
{
	$event->link('Participants', array($char));
	$event->save();
}
else
{
	if (-1 == $char)
	{
		echo tag('div', array('id' => 'selectChar'), $account->getCharactersList(true));
		jQ('
function choosePerso(char)
{
followLink("' . to_url(array(
			'controller' => 'EventParticipant',
			'action' => 'join',
			'id' => $event['id'],
			'char' => ''
		)) . '" + char);
}
$("#selectChar").accordion({clearStyle: true, collapsible: true, active: false})');
	}
	else
		echo lang('shop.!character_on_acc');
}
redirect(array('controller' => 'Event', 'action' => 'index', 'year' => substr($event->period, 0, 4), 'month' => substr($event->period, 5, 2)));
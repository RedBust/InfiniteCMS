<?php
if (!check_level(LEVEL_LOGGED))
	return;
if (!$account->canParticipate($id = intval($router->requestVar('id', -1))))
{
	echo lang('event.cant_participate');
	return;
}

if (( !$event = EventTable::getInstance()
							->createQuery('e')
								->leftJoin('e.Guild g')
							->where('e.id = ?', $id)
							->fetchOne() ))
{
	echo lang('event.does_not_exist');
	return;	
}
if ($event->isElapsed())
{
	echo lang('event.elapsed');
	return;
}
if ($event->isFull())
{
	echo lang('event.full');
	return;
}

$char = $account->getMainChar();
if ($char && (!$event->relatedExists('Guild')
  || ($event->relatedExists('Guild') && $char->relatedExists('GuildMember') && $char->GuildMember->relatedExists('Guild') && $event->Guild->id == $char->GuildMember->Guild->id)))
{
	$event->link('Participants', array($char->guid));
	if ($event->is_tombola && $event->isFull()) //LET'S GO
		$event->doTombola();
	$event->save();
	redirect($event->getUrl());
}
else
	define('HTTP_CODE', 404);
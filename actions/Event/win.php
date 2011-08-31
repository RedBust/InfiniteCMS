<?php
if (!check_level(LEVEL_MJ))
	return; //MJ are allowed to select winner

if (!( $event = EventTable::getInstance()
						->createQuery('e')
							->leftJoin('e.Participants p')
							->leftJoin('e.Reward r')
								->leftJoin('r.Effects re')
						->where('id = ?', $id = $router->requestVar('id'))
						->fetchOne() ))
{
	define('HTTP_CODE', 404);
	return;
}
if (!$event->isElapsed())
{
	echo lang('event.!elapsed');
	return;
}
if ($event->relatedExists('Winner'))
{
	echo lang('event.already_won');
	return;
}

$charName = $router->requestVar('char');
$char = null;
if ($charName !== NULL)
{
	foreach ($event->Participants as $p)
	{
		if ($p->name == $charName)
		{ //I wish I could use array_search here. gonna write _array_search($ary, $val, $callBack || $key). meeeh ... too lazy
			$char = $p;
			break;
		}
	}
}
if ($char === NULL)
{
	echo lang('character.!on_event');
	return;
}
if ($event->relatedExists('Reward'))
	$event->Reward->giveTo($p);

$event->Winner = $p;
$event->save();
redirect($event->getURL());
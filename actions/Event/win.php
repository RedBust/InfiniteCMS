<?php
if (!check_level(LEVEL_LOGGED))
	return; //MJ are allowed to select winner

$router->codeUnless(404, $event = EventTable::getInstance()
						->createQuery('e')
							->leftJoin('e.Participants p')
							->leftJoin('e.Reward r')
								->leftJoin('r.Effects re')
						->where('id = ?', $id = $router->requestVar('id'))
						->fetchOne());
if (!$account->canSetWinner($event) || $event->isFinished())
{
	define('LEVEL_FALLBACK', true);
	return;
}
if (!$event->isWinnable())
{
	echo lang('event.!elapsed');
	return;
}

if ($event->is_tombola)
{
	if ($event->capacity == -1)
		$event->doTombola();
}
else
{
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
	$event->setWinner($p);
}
$event->save();
redirect($event->getURL());
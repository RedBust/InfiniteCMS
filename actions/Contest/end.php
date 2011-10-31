<?php
if (!check_level(LEVEL_ADMIN))
	return;

$router->codeUnless(404, $contest = ContestTable::getInstance()->retrieve('id'));
$router->codeIf(404, $contest->ended);
vdump($contest);
if ($contest->Participants->count())
{
	$i = 1;
	$lastVotes = $contest->Participants->getFirst()->votes;
	$places = array();
	foreach ($contest->Participants as $participant)
	{
		if ($lastVotes != $participant->votes)
		{
			if (++$i > $config['LADDER_LIMIT'])
				break;
			$lastVotes = $participant->votes;
		}

		//@todo
		$participant->position = $i;
		$places[$i][] = $participant;
		echo $participant->position . ' ' . $participant->votes . tag('br');
	}

	if ($contest->relatedExists('Reward'))
	{
		foreach ($places[1] as $winner)
			$contest->Reward->giveTo($winner->Character);
	}
}
$contest->ended = true;
$contest->save();
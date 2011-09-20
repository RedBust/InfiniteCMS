<?php
$polls = Query::create()
			->from('Poll p INDEXBY p.id')
				->leftJoin('p.Options o')
					->leftJoin('o.Polleds u'); //User
if (!level(LEVEL_ADMIN))
{ //PLAEZ DO NOT OVERDID THAT. Don't put OVER NINE THOUSANDS POLLS in the same daterange. kthx.
	$polls->where('NOW() BETWEEN p.date_start AND p.date_end'); //Doctrine_Expression does not work. Don't wanna seek why

	$lastElapsedPoll = Query::create()
								->from('Poll p')
									->leftJoin('p.Options o')
								->where('p.date_end < NOW()')
								->limit(1)
								->fetchOne();
}
$polls = $polls->execute();

if (!empty($lastElapsedPoll))
	$polls->add($lastElapsedPoll);


if ($polls->count() || $lastElapsedPoll)
{
	foreach ($polls as $poll)
	{ /* @var $poll Poll */
		if (!$poll->Options->count() && !level(LEVEL_ADMIN))
			continue;
		partial('_show', array('poll'), PARTIAL_CONTROLLER);
	}
}
else
	echo tag('b', lang('poll.any'));

if (level(LEVEL_ADMIN))
	echo make_link('@poll.new', lang('poll.new'));
<?php
$pollsQ = Query::create()
			->from('Poll p')
				->leftJoin('p.Options o')
					->leftJoin('o.Polleds u'); //User
if( !level( LEVEL_ADMIN ) )
{ //PLAEZ DO NOT OVERDID THAT. Don't put OVER NINE THOUSANDS POLLS in the same daterange. kthx.
	$pollsQ->where('p.date_start < ?', new Doctrine_Expression('NOW()'))
			->andWhere('p.date_end > NOW()');
}
$polls = $pollsQ->execute();
$pollsQ->free();

if ($polls->count())
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
{ //display the "create" link if admin?
	echo make_link('@poll.new', lang('poll.new'));
}
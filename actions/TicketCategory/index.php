<?php
$categories = Query::create()
					->select('tc.*')
					->from('TicketCategory tc')
						->leftJoin('tc.Tickets t')
					->execute();
if ($categories->count())
{
}
else if (!check_level(LEVEL_ADMIN))
	return;

echo make_link('@ticket_category.new', lang('ticket_category.new'));
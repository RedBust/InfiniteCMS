<?php

/**
 * EventTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class EventTable extends RecordTable
{
	protected $_eventBoxes = null;

    /**
     * Returns an instance of this class.
     *
     * @return object EventTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Event');
    }

	//AndM = And Maybe (yeah, I know, the door is fully opened ._.)
	public function findByYearMonthAndMGuildId($dates, $guild)
	{
				$d = array();
		$args = array();
		foreach ($dates as $date)
		{
			$d[] = '(YEAR(e.period) = ? AND MONTH(e.period) = ?)';
			$args[] = $date[0];
			$args[] = $date[1];
		}

		return $this->createQuery('e')
					->where('(guild_id = ? OR guild_id = 0 OR guild_id IS NULL)', $guild)
						->andWhere(implode(' OR ', $d), $args)
						->leftJoin('e.Participants p INDEXBY guid')
						->leftJoin('e.Guild g')
					->execute();
	}


	public function sendEventBoxes()
	{
		if (null !== $this->_eventBoxes)
			return;

		global $account;
		$this->_eventBoxes = array();

		echo tag('div', array('style' => array('display' => 'none'), 'id' => 'eventParticipants', 'title' => lang('participants')), '');

		jQ('
var eventParticipants = $("#eventParticipants").dialog(dialogOpt),
	events = [];

function showEvent(id)
{
	eventParticipants.dialog("open").find(".event").hide();
	eventParticipants.find("#event-" + id).show();
}
function registerEvent(id)
{
	events[id] = $("#event-" + id).addClass("event").appendTo(eventParticipants);
}
pageBind(function ()
{
	eventParticipants.dialog("close");
	delete eventParticipants;
	delete events;
})');
	}

	public function sort($events)
	{
		global $account;
		$this->sendEventBoxes();

		$days = array();
		foreach ($events as $event)
		{
			$days[$event->getDay()][] = $event;
		}
		return $days;
	}
	public function display($events, $day)
	{
		if (isset($events[$day]))
		{	
			return tag('div', array('align' => 'center'), tag('b', tag('u', $day))) . tag('br') .
			 implode(tag('br'), $events[$day]);
		}
		return tag('b', $day);
	}
}
<?php

/**
 * Event
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Vendethiel <vendethiel@hotmail.fr>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Event extends BaseEvent
{
	protected $elapsed = null;

	public function isElapsed()
	{
		if (null === $this->elapsed)
		{
			list($date, $time) = explode(' ', $this->period);
			list($y, $mo, $d) = explode('-', $date);
			list($h, $mi, $s) = explode(':', $time);

			$this->elapsed = date_passed(mktime($h, $mi, $s, $mo, $d, $y));
		}
		return $this->elapsed;
	}
	public function getDay()
	{
		return substr($this->period, 8, 2);
	}
	public function getHourMinutes()
	{
		return substr($this->period, 11, 5);
	}

	public function getGuildLink()
	{
		if (!$this->relatedExists('Guild'))
			return '';
		return make_link($this->Guild, '[G]');
	}

	public function __toString()
	{
		global $account;

		$canParticipate = level(LEVEL_LOGGED) ? $account->canParticipate($this->id) : false;
		$participate_url = to_url(array('controller' => 'EventParticipant', 'action' => $canParticipate ? 'join' : 'part', 'id' => $this->id));

		if ($this->Participants->count())
		{
			$participants = array();
			foreach ($this->Participants as $character)
			{
				$participants[] = tag('span', array('class' => $character->isMine() ? 'myChar' : 'aChar'), make_link($character));
			}
			$participants = implode(', ', $participants);
		}
		else
			$participants = $this->isElapsed() ? '' : lang('participants.any');

		if ($this->isElapsed())
			$participate_link = tag('i', lang('event.elapsed')) . tag('br');
		else
		{
			if (level(LEVEL_LOGGED))
			{
				$participate_link = make_img('icons/group_' . ($canParticipate ? 'add' : 'delete'), EXT_PNG) .
				 tag('b', make_link($participate_url, lang('event.' . ($canParticipate ? 'join' . ($this->Participants->count() ? '' : '_first') : 'part')), array(), array(), false)) . tag('br');
			}
			else
				$participate_link = '';
		}
		echo tag('div', array('id' => 'event-' . $this->id, 'class' => 'showThis'), $participate_link . $participants);
		jQ('registerEvent(' . $this->id . ')');

		return tag('b', str_replace(':', 'h', $this->getHourMinutes()) . ($this->getGuildLink()) . ': ') . $this->name .
		 ($this->isElapsed() && !$this->Participants->count() ? '' : js_link('showEvent(' . $this->id . ')', make_img('icons/group', EXT_PNG, lang('participants')), '#', array('class' => 'showThis'))) .
		 (level(LEVEL_LOGGED) && !$this->isElapsed() && $account->getMainChar() ? make_link($participate_url, 
		   make_img('icons/group_' . ($canParticipate ? 'add' : 'delete'), EXT_PNG, lang('participant.join')),
		   null, array('class' => 'hideThis')) : '');
	}
}
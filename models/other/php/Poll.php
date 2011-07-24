<?php

/**
 * Poll
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-D0C <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Poll extends BasePoll
{
	protected $total = -1;

	public function countPolleds()
	{
		if ($this->total === -1)
		{
			$this->total = 0;
			foreach( $this->Options as $option )
			{ /* @var $option PollOption */
				$this->total += $option->Polleds->count();
			}
		}
		return $this->total;
	}
	public function getName()
	{
		return lang($this->_get('name'), 'common', '%%key%%');
	}
	public function getRawName()
	{
		return $this->_get('name');
	}

	public function maj($vals)
	{
		$errors = array();

		if (!empty($vals['name']))
			$this->name = $vals['name'];
		else if (!$this->exists())
			$errors['name'] = sprintf(lang('must_!empty'), lang('name'));

		$pre_dates = $dateTimes = array();
		foreach (array('start', 'end') as $date_type)
		{
			if (empty($vals['date_' . $date_type]))
			{
				if ($this->exists())
					continue;
				else
					$errors['date_' . $date_type] = sprintf(lang('must_!empty'), lang('poll.date_' . $date_type));
			}
			else
			{
				$pre_dates[$date_type] = $this->{'date_' . $date_type};
				if (( $dateTimes[$date_type] = date_from_picker($vals['date_' . $date_type]) ) instanceof DateTime)
					$this->{'date_' . $date_type} = $dateTimes[$date_type]->format('Y-m-d');
				else
				{
					$errors['date_' . $date_type] = sprintf(lang('incorrect_date'), lang('poll.date_' . $date_type));
				}
			}
		}
		if (empty($errors['date_start']) && empty($errors['date_end'])
		 && !empty($this->date_start) && !empty($this->date_end)
		 && date_from_picker($this->date_start)->getTimestamp() > date_from_picker($this->date_end)->getTimestamp())
		{
			if (isset($pre_dates['start']))
				$this->date_start = $pre_dates['start'];
			if (isset($pre_dates['end']))
				$this->date_end = $pre_dates['end'];
			$errors['date_start'] = sprintf(lang('incorrect_date'), lang('poll.date_start'));
		}

		if (empty($errors))
			$this->save();
		return $errors;
	}
	public function setUp()
	{
		parent::setUp();
		$this->hasAccessor('name', 'getName');
	}
}
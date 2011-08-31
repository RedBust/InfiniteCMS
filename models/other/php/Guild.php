<?php

/**
 * Guilds
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-Doc <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: Guild.php 25 2010-10-22 12:10:44Z nami.d0c.0 $
 */
class Guild extends BaseGuild
{
	public $totalCharsLevels = 0,
	$countChars = 0;
	protected $emblemCalled = 1;

	public function getMeanLevel()
	{
		return round($this->totalCharsLevels / $this->Members->count(), 0);
	}

	public function getLink($text = NULL)
	{
		if ($text === NULL)
			$text = $this->getName();

		return make_link(array('controller' => 'Guild', 'action' => 'show', 'id' => $this->id), $text, array('title' => $this->name));
	}

	public function getName()
	{
		return $this->getEmblem() . ' ' . $this->name;
	}

	public function getEmblem()
	{
		if (empty($this->emblem))
			return '';

		$emblem = explode(',', strtoupper($this->emblem));
		$this->getTable()->initEmblems();
		$emblemCode = '';
		/**
		 $emblemCode = '<object >';
		 */
		return $emblemCode;
	}
}

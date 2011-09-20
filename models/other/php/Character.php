<?php

/**
 * Character
 *
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-Doc <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: Personnage.php 49 2010-12-10 16:25:06Z nami.d0c.0 $
 */
class Character extends BaseCharacter
{
	/** @var $items Item[] */
	protected $items = NULL;
	/** @var $sorts int[][] */
	protected $sorts = NULL;
	/** @var $spellRange char[][] */
	protected $spellRange = NULL;

	public function getMap()
	{
		return MapTable::getInstance()->find($this->map);
	}

	public function toString($includeName = true, $accordion = false)
	{
		return ( $includeName ? ($accordion ? '<h3>' : tag('b', lang('character_name') . ': ')) . $this->getName() . tag('br') . ($accordion ? '</h3>' : '') : '' ) .
		($accordion ? '<div><p>' : '') . tag('b', lang('acc.ladder.class') . ': ') . $this->getBreed() . tag('br') .
		tag('b', lang('acc.ladder.sex') . ': ') . $this->getGender() . tag('br') .
		tag('b', lang('level') . ': ') . $this->level . tag('br');
	}
	public function getName()
	{
		if ($this->Account->getMainChar()->guid == $this->guid)
			return tag('u', $this->name);
		return $this->name;
	}
	//is guild master
	public function isGM($guild = NULL)
	{
		if (!$this->relatedExists('GuildMember'))
			return false;
		if ($this->GuildMember->rank != 1)
			return false;
		if ($guild == NULL || $guild == -1)
			return true;
		return $this->GuildMember->guild == $guild;
	}

	public function __toString()
	{
		return $this->toString();
	}

	public function asTableRow()
	{
		return tag('tr', $this->getTableRowDatas());
	}

	public function getTableRowDatas($simple = false)
	{
		$datas = tag('td', $this->getLink());
		if (!$simple)
		{
			$datas .= tag('td', $this->getBreed()) .
					tag('td', $this->getGender()) .
					tag('td', $this->level);
		}
		return $datas;
	}

	public function getBreed()
	{
		return IG::getBreed($this->class);
	}

	public function getGender()
	{
		return IG::getGender($this->sexe);
	}

	protected function _init()
	{
		Collection::charLoad($this);
	}

	public function getURL()
	{
		return array(
			'controller' => 'Character',
			'action' => 'show',
			'id' => $this->guid);
	}

	public function getLink($text = NULL)
	{
		$this->_init();
		return js_link(sprintf('showChar(%d)', $this->guid), $text === NULL ? html($this->name) : $text, $this->getURL());
	}

	public function getInfoBox()
	{
		return $this . tag('br') . make_link($this->getURL(), lang('more') . ' ...', array(), array('id' => 'closeProfilBox'));
	}

	/**
	 * getItems
	 * returns all the items of this character
	 *
	 * @return Item[] All the items of the character
	 */
	public function getItems()
	{
		if ('' == trim($this->objets))
			return array();

		if ($this->items === NULL)
		{
			$this->items = Query::create()
					->from('Item')
						->whereIn('guid', explode('|', $this->objets))
					->execute();
		}
		return $this->items;
	}

	public function getSpells()
	{
		if ($this->sorts === NULL)
		{
			if (empty($this->spells))
			{
				$this->sorts = array();
				return;
			}

			//$this->spells = spellID;spellLevel;spellPosition,ID2;lvl2;pos2,...
			$sorts = explode(',', $this->spells);
			$spells = array();
			//$spells = [[spellID, spellLevel, position]]
			foreach ($sorts as $sort)
			{
				$spells[] = explode(';', $sort);
			}
			$this->sorts = $spells;
		}
		return $this->sorts;
	}

	/**
	 * getSpellCount
	 * return number of spells
	 *
	 * @return integer spell count
	 */
	public function getSpellCount()
	{
		return count($this->getSpells());
	}

	/**
	 * getSpellAvgLevel
	 * return the mean of the levels of spells
	 *
	 * @return integer mean
	 */
	public function getSpellAvgLevel()
	{
		$spellLevels = 0;
		foreach ($this->getSpells() as $sort)
		{
			$spells += $sort[2];
		}
		return $spellLevels / $this->getSpellCount();
	}

	/**
	 * getSpellPos
	 * return the spell Pos
	 *
	 * @return string
	 */
	public function getSpellPos($p)
	{
		if ($p === '_')
			return lang('pos.no', 'spell');
		$this->_initSpellRange();
		if (in_array($p, $this->spellRange[0]))
		{
			$pos = array_search($p, $this->spellRange[0]);
			$line = lang('first');
		} else
		{
			$pos = array_search($p, $this->spellRange[1]);
			$line = lang('second');
		}
		return sprintf(lang('pos.line', 'spell'), $line, $pos + 1);
	}

	protected function _initSpellRange()
	{
		if ($this->spellRange === NULL)
			$this->spellRange = array(
				range('a', 'g'),
				range('h', 'n'),
			);
	}

	public function isMine($accountID = null)
	{
		if (!level(LEVEL_LOGGED))
			return false;

		if ($accountID instanceof Account)
			$accountID = $accountID->guid;
		if (!$accountID)
		{
			global $account;
			$accountID = $account->guid;
		}
		return $this->account == $accountID;
	}
}

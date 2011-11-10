<?php

/**
 * GuildMember
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-Doc <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: GuildMember.php 24 2010-10-22 11:46:07Z nami.d0c.0 $
 */
class GuildMember extends BaseGuildMember
{
	public function __toString()
	{
		if (!$this->relatedExists('Character'))
			return ''; //wtf ?
		$this->initRights();
		$this->Guild->totalCharsLevels += $this->Character->level; //add the level of the character to the total lvl
		return tag('tr', tag('td', lang('guild.rank.' . $this->rank)) .
		 $this->Character->getTableRowDatas(true) .
		 tag('td', number_format($this->xpdone, 0, '', ' ')) .
		 tag('td', $this->pxp) .
		 tag('td', array('class' => 'showMe'), js_link('showRightsPanel(' . $this->getRights() . ')', '...')));
	}

	public function getRights()
	{
		global $guildRights;
		if ($this->rank == 1)
			return array_sum($guildRights);
		else
			return $this->rights;
	}
	protected function initRights()
	{
		global $guildRights;
		$rights = $this->getRights();

		$this->getTable()->initRightsPanel();
		jQ('registerRightsPanel(' . $rights . ')');

		if ($cache = Cache::start('Guild_rights_' . $rights))
		{
			$html = '';
			$haveRights = array();
			foreach ($guildRights as $k)
				$haveRights[$k] = false;
			while ($rights > 0)
			{
				foreach (array_reverse($guildRights) as $k)
				{
					if ($k <= $rights)
					{
						$haveRights[$k] = true;
						$rights -= $k;
						break;
					}
				}
			}
			foreach ($haveRights as $k => $have)
				$html .= input('right[]', NULL, 'checkbox', $have, array('disabled' => 'disabled')) . lang('guild.right.' . $k) . tag('br');

			echo tag('div', array('style' => array('display' => 'none'), 'class' => 'rights', 'id' => 'rights' . $this->getRights()), $html);
			$cache->save(Cache::SHOW, Cache::NO_JS);
		}
	}
}
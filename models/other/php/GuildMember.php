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
		$this->Guild->totalCharsLevels += $this->Character->level; //add the level of the character to the total lvl
		return tag('tr', tag('td', lang('guild.rank.' . $this->rank)) .
			$this->Character->getTableRowDatas(true) .
			tag('td', number_format($this->xpdone, 0, '', ' ')) .
				tag('td', $this->pxp));
	}
}
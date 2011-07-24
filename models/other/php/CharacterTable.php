<?php

/**
 * CharacterTable
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-Doc <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: PersonnageTable.php 45 2010-12-04 13:13:19Z nami.d0c.0 $
 */
class CharacterTable extends RecordTable
{
	/**
	 * returns table header (with tr)
	 *
	 * @param boolean $simple Simple datas ? (only name)
	 * @return string HTML format of datas header with TR
	 */
	public function getTableHeader($simple = false)
	{
		return tag('tr', $this->getTableHeaderDatas($simple));
	}

	/**
	 * returns the datas header for a table
	 *
	 * @param boolean $simple Simple datas ? (only name)
	 * @return string HTML format
	 */
	public function getTableHeaderDatas($simple = false)
	{
		$datas = tag('th', tag('b', lang('name')));
		if (!$simple)
		{
			$datas .= tag('th', tag('b', lang('acc.ladder.class'))) .
					tag('th', tag('b', lang('acc.ladder.sex'))) .
					tag('th', tag('b', lang('level')));
		}
		return $datas;
	}

	public function retrieve()
	{
		global $router;

		$col = $this->getIdentifier();
		$c = -1;
		foreach ($this->getColumnNames() as $col) //Character/show/name/...
			if (( $c = $router->requestVar($col == 'guid' ? 'id' : $col) ) !== NULL)
				break;
		return $this->createQuery('p')
							->leftJoin('p.GuildMember gm')
								->leftJoin('gm.Guild g')
							->where('p.' . $col . ' = ?', $c)
						->fetchOne(); //find the character and fetch his guild
	}
}

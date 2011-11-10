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
	public function getSearch()
	{
		global $sBreed, $sGender, $breed, $gender, //this looks like a phpBB's function header x(
			$contest, $char, $m,
			$orders, $ordersLang, $orderBy, $breeds, $genders;

		jQ('
var searchForm = $("fieldset#searchFS").find("div"),
	searchVisible = ' . ( $gender == -1 && $breed == -1 && empty($char) && $orderBy == end($orders) && $m == 'DESC' ? 'false' : 'true' ) . ';
	searchLegend = $("#searchLegend").click(function ()
{
	console.log("triggered");
	searchForm.slideToggle();
	searchVisible = !searchVisible; //DON\'T ASK ME WHY searchForm.is(":visible") DOESN\'T WORK, I AIN\'T GOT A CLUE ! SHITTY LIB
	if (searchVisible) //and just calling searchForm.is(":visible") in the browser\'s console works fine ... FUCK YOU.
		searchLegend.find("span").html(" <");
	else
		searchLegend.find("span").html(" >");
});');
		return tag('fieldset', array('id' => 'searchFS'), tag('legend', array('id' => 'searchLegend'), lang('character.search') . tag('span', array('class' => 'showThis'), $sGender == -1 && $sBreed == -1 && empty($char) ? ' >' : ' <')) .
		 tag('div', array('class' => $sGender == -1 && $sBreed == -1 && empty($char) && $orderBy == end($orders) && $m == 'DESC' ? 'hideThis' : ''), make_form(array(
			array('character', lang('name'), NULL, $char),
			array('orderBy', lang('ladder.order_by'), 'select', $ordersLang, $orderBy),
			array('orderMode', lang('ladder.order_mode'), 'select',
			 array('DESC' => lang('ladder.order_mode.DESC'), 'ASC' => lang('ladder.order_mode.ASC')), $m),
			array('gender', lang('acc.ladder.sex'), 'select', $genders, $sGender),
			array('breed', lang('acc.ladder.class'), 'select', $breeds, $sBreed),
			array('contest', NULL, 'hidden', $contest ? $contest->id : -1),
		)))) . tag('br');
	}

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
	 * @param boolean $simple simple datas ? (only name)
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

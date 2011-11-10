<?php

/**
 * ItemTable
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-Doc <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: ItemsTable.php 24 2010-10-22 11:46:07Z nami.d0c.0 $
 */
class ItemTable extends RecordTable
{
	public function getPosOffset()
	{
		return array(
			0 => 'width:40px; height:40px; margin:42px 0 0 220px;',
			1 => 'width:56px; height:56px; margin:24px 0 0 317px;',
			2 => 'width:40px; height:40px; margin:105px 0 0 119px;',
			3 => 'width:55px; height:55px; margin:102px 0 0 212px',
			4 => 'width:40px; height:40px; margin:105px 0 0 327px',
			5 => 'width:55px; height:55px; margin:198px 0 0 212px;',
			6 => 'width:48px; height:48px; margin:24px 0 0 421px;',
			7 => 'width:48px; height:48px; margin:89px 0 0 421px;',
			8 => 'width:48px; height:48px; margin:153px 0 0 421px;',
			9 => 'width:31px; height:31px; margin:19px 0 0 27px;',
			10 => 'width:31px; height:31px; margin:61px 0 0 27px;',
			11 => 'width:31px; height:31px; margin:104px 0 0 27px;',
			12 => 'width:31px; height:31px; margin:147px 0 0 27px;',
			13 => 'width:31px; height:31px; margin:189px 0 0 27px;',
			14 => 'width:31px; height:31px; margin:231px 0 0 27px;',
			15 => 'width:56px; height:56px; margin:24px 0 0 112px;',
			'dinde' => 'width:48px;height:48px;margin:218px 0 0 421px;',
			'kamas' => 'width:120px; height:22px; color:#f5f4f2; margin:265px 0 0 76px; text-align:center; font:12px Verdana; font-weight:bold;',
		);
	}
}
<?php

/**
 * LiveActionTable
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-Doc <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: LiveActionTable.php 24 2010-10-22 11:46:07Z nami.d0c.0 $
 */
class LiveActionTable extends RecordTable
{
	public function give($char, $objet)
	{
		global $types, $config;
		if (!isset($types[$objet['type']]))
			return;

		$item = new self;
		$item->Character = $char;
		$item->action = $objet['type'];
		$item->nombre = $objet['value'];
		$item->save();
	}
}
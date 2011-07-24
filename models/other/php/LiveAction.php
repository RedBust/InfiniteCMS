<?php

/**
 * LiveAction
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-Doc <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: LiveAction.php 24 2010-10-22 11:46:07Z nami.d0c.0 $
 */
class LiveAction extends BaseLiveAction
{
	public static function giveItem($perso, $objet)
	{
		global $types, $config;
		if (!isset($types[$objet['type']]))
			return;

		$item = new self;
		$item->Character = $perso;
		$item->action = $objet['type'];
		$item->nombre = $objet['value'];
		$item->save();
	}
}

<?php

/**
 * ItemTemplate
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-Doc <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: ItemTemplate.php 24 2010-10-22 11:46:07Z nami.d0c.0 $
 */
class ItemTemplate extends BaseItemTemplate
{
	public function __toString()
	{
		return lang( $this->id, 'item', '%%key%%' );
	}

	public function parseStats($isMax)
	{
		return IG::parseStats( $this->statstemplate, $isMax );
	}
}

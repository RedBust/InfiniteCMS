<?php

/**
 * ItemEffect
 *
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-Doc <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: ItemEffect.php 40 2010-11-21 01:15:23Z nami.d0c.0 $
 */
class ShopItemEffect extends BaseShopItemEffect
{
	public function getValue()
	{
		if( $this->isItem() )
			return $this->getTable()->doFindItemTemplate( $this );
		return $this->value;
	}
	public function setValue($val)
	{
		if( $this->isItem() ) //find the item ID
		{
			if( is_numeric( $val ) ) //itemID submitted
			{
				if( lang( $val, 'item', NULL ) === NULL )
					return false; //item does not exists
			}
			else
			{
				if( ( $val = array_search( $val, lang( NULL, 'item' ) ) ) === false )
				{
					if( ( $val = array_search( html( $val ), lang( NULL, 'item' ) ) ) === false )
						return false; //item does not exists
				}
			}
		}
		$this->value = $val;
		return true;
	}

	public function isItem()
	{
		return $this->getTable()->isItem( $this );
	}
}

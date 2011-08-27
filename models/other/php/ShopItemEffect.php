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
	public function __toString()
	{
		if ($this->type === NULL || $this->type == -1 //should not happen but ...
			|| (!$this->isItem() && $this->getValue() === 0 ))
			continue; //null effect ?

		$signe = ''; //+ or -
		$showType = true;

		$val = $this->getValue(); //the "real" value
		if ($this->isItem())
		{ /* @var $val ItemTemplate */
			$showType = false; //don't show the type
			$color = 'green'; //add

			$val = '</u>' . make_img('items/' . $this->value, EXT_PNG, array(
				'style' => 'width: 50px; height: 50px;',
				'class' => 'showEffects',
				'data-id' => $val instanceof ItemTemplate ? $val->id : '',
				'title' => $this->getItemStats(),
			)) . '<span class="hideThis">:</span> <u>';
		} //end if effect::isItem
		else
		{ //+ = green, - = red
			$color = $val > 0 ? 'green' : 'red';
			$signe = $val > 0 ? '+' : '-';
		}

		if (!isset($types[$this->type]))
			vdump($this->type);
		$type = $types[$this->type];
		if ($type[0] == $signe)
			$type = substr($type, 1);
		return tag('li', array('style' => array('color' => $color)), '<b>' . $signe . '</b><u>' . $val . '</u> '
				. ( $showType ? $type : '<span class="hideThis">' . $type . '</span>' ));
	}

	public function isItem()
	{
		return $this->getTable()->isItem( $this );
	}
	public function isMaxStats()
	{
		return $this->type == ShopItemEffectTable::TYPE_ITEM_JETS_MAX;
	}
	public function getItemStats()
	{
		if (!$this->isItem())
			return '';

		if (( $val = $this->getValue() ) instanceof ItemTemplate
		 && !empty($val->statstemplate))
			return str_replace('"', "'", $val->parseStats($this->isMaxStats()));
		return '';
	}

	public function getDeleteLink()
	{
		return make_link(array('controller' => $router->getController(), 'action' => 'delete', 'mode' => 'ItemEffect', 'id' => $effect->id), lang('act.delete'));
	}
}
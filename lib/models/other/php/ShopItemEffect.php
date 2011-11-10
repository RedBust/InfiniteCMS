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
		if ($this->isItem())
			return $this->getTable()->doFindItemTemplate($this);
		return $this->value;
	}
	public function setValue($val)
	{
		if ($this->isItem()) //find the item ID
		{
			if (is_numeric($val)) //itemID submitted
			{
				if(lang($val, 'item', NULL) === NULL)
					return false; //item does not exists
			}
			else
			{
				if (( $val = array_search($val, lang(NULL, 'item'))) === false )
				{
					if (( $val = array_search(html($val), lang(NULL, 'item')) ) === false )
						return false; //item does not exists
				}
			}
		}
		$this->value = $val;
		return true;
	}
	public function __toString()
	{
		if ($this->type === NULL || $this->type == -1 || $this->getValue() === 0)
			return ''; //null effect ?

		if ($this->isLiveAction())
			return LiveActionTable::getInstance()->render($this);
		else
			return $this->renderEffect();
	}

	public function isLiveAction()
	{
		return $this->getTable()->isLiveAction($this);
	}
	public function isItem()
	{
		return $this->getTable()->isItem($this);
	}
	public function isMaxStats()
	{
		return $this->type == LiveActionTable::TYPE_ITEM_JETS_MAX;
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
		return make_link(array('controller' => 'Shop', 'action' => 'delete', 'mode' => 'ItemEffect', 'id' => $this->id), lang('act.delete'));
	}

	public function giveTo($char)
	{
		if ($this->isLiveAction())
			LiveActionTable::getInstance()->give($char, $this);
		else
			$this->processEffect($char);
	}

	public function processEffect($char)
	{
		global $account;
		switch ($this->type)
		{
			case ShopItemEffectTable::TYPE_ADD_PREFIX:
				$mainChar = $account->getMainChar();
				$name = $mainChar->name;
				if ($pos = strpos($name, ']')) //remove actual prefix
					$name = substr($name, $pos+1);
				$name = sprintf('[%s]%s', $this->value, $name);
				$mainChar->name = $name;
				//$account->save() should be enough.
			break;
		}
	}
	/**@todo that way ?
	$effectLangs = array(
		ShopItemEffectTable::TYPE_ADD_PREFIX => 'Adds prefix <i>%s</i>',
	);
	//*or ...
	public function renderEffect()
	{
		return strtr(lang('shop.effect.' . $this->type), array(
			'%value%' => $this->getValue(),
			'%val%' => $this->value,
		));
	}
	*/
	public function renderEffect()
	{
		switch ($this->type)
		{
			case ShopItemEffectTable::TYPE_ADD_PREFIX:
				return lang('character.prefix_name') . ' : ' . tag('i', $this->value);
			break;
		}
	}
}
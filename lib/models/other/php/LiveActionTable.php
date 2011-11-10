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
	const TYPE_LEVEL_UP = 1,
		TYPE_ADD_XP = 2,
		TYPE_ADD_K = 3,
		TYPE_ADD_CAPITAL = 4,
		TYPE_ADD_SPELLPOINT = 5,

		TYPE_ITEM_JETS_ALEATOIRES = 20,
		TYPE_ITEM_JETS_MAX = 21,

		TYPE_CARAC_FORCE = 118,
		TYPE_CARAC_AGILITE = 119,
		TYPE_CARAC_CHANCE = 123,
		TYPE_CARAC_SAGESSE = 124,
		TYPE_CARAC_VITALITE = 125,
		TYPE_CARAC_INTELLIGENCE = 156;

	public function give($char, $item)
	{
		global $types, $config;
		if (!in_array($item['type'], $this->getTypes()))
			return;

		$la = new LiveAction;
		$la->Character = $char;
		$la->action = $item['type'];
		$la->nombre = $item['value'];
		$la->save();
	}

	public function getTypes()
	{ //crappy.
		return array(self::TYPE_LEVEL_UP, self::TYPE_ADD_XP, self::TYPE_ADD_K, self::TYPE_ADD_CAPITAL, self::TYPE_ADD_SPELLPOINT,
		 self::TYPE_ITEM_JETS_ALEATOIRES, self::TYPE_ITEM_JETS_MAX,
		 self::TYPE_CARAC_FORCE, self::TYPE_CARAC_AGILITE, self::TYPE_CARAC_CHANCE, self::TYPE_CARAC_SAGESSE, self::TYPE_CARAC_VITALITE, self::TYPE_CARAC_INTELLIGENCE);
	}
	public function getItemTypes()
	{
		return array(LiveActionTable::TYPE_ITEM_JETS_ALEATOIRES, LiveActionTable::TYPE_ITEM_JETS_MAX);
	}

	public function render(ShopItemEffect $effect)
	{
		global $types;
		$sign = ''; //+ or -
		$showType = true;

		$val = $effect->getValue(); //the "real" value
		if ($effect->isItem())
		{ /* @var $val ItemTemplate */
			$showType = false; //don't show the type
			$color = 'green'; //add

			$val = '</u>' . make_img('items/' . $effect->value, EXT_PNG, array(
				'style' => 'width: 50px; height: 50px;',
				'class' => 'showEffects',
				'data-id' => $val instanceof ItemTemplate ? $val->id : '',
				'title' => $effect->getItemStats(),
			)) . '<span class="hideThis">:</span> <u>';
		}
		else
		{ //+ = green, - = red
			$color = $val > 0 ? 'green' : 'red';
			$sign = $val > 0 ? '+' : '-';
		}

		if (!in_array($effect->type, $this->getTypes()))
			exit('unknow type : ' . $effect->type);
		$type = ShopItemTable::getInstance()->getType($effect->type);
		if ($type[0] == $sign) //remove +
			$type = substr($type, 1);
		return tag('span', array('style' => array('color' => $color)), '<b>' . $sign . '</b><u>' . $val . '</u> ' .
		 ( $showType ? $type : '<span class="hideThis">' . $type . '</span>' ));
	}
}
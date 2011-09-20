<?php

/**
 * ItemEffectTable
 *
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-Doc <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: ItemEffectTable.php 24 2010-10-22 11:46:07Z nami.d0c.0 $
 */
class ShopItemEffectTable extends RecordTable
{
	const TYPE_ADD_PREFIX = 300;


	/**
	 * items
	 *
	 * @var ItemTemplate[]
	 * @static
	 * @access public
	 */
	public $items = null;

	/**
	 * _initItem
	 * Initialise all items. see {@$items}
	 *
	 * @access protected
	 * @static
	 * @return void
	 */
	protected function _initItems()
	{
		if( $this->items === array() )
			$this->items = lang(NULL, 'item');
	}

	/**
	 * Determinates if the type is an item
	 *
	 * @param integer $type The type of the item
	 *
	 * @return boolean whether it's an item or not ?
	 */
	public function isItem($type)
	{
		if ($type instanceof ShopItemEffect || is_array($type))
			$type = $type['type'];

		return in_array(intval($type), LiveActionTable::getInstance()->getItemTypes());
	}
	public function isLiveAction($type)
	{
		if ($type instanceof ShopItemEffect || is_array($type))
			$type = $type['type'];

		return in_array(intval($type), LiveActionTable::getInstance()->getTypes());
	}

	/**
	 * doFindItem
	 * find a item name by it's ID, with a litte cache.
	 *
	 * @access public
	 * @static
	 *
	 * @param integer $id The Id of the object
	 * @return mixed boolean|error The name of the object or "Objet inconnu"
	 */
	public function doFindItem($id)
	{
		$this->_initItem();
		if ($id instanceof ItemEffect)
			return $this->doFindItemIf($id->value, $this->isItem($id));

		$id = intval($id);
		return isset($this->items[$id]) ? $this->items[$id] : sprintf(lang('shop.item.not_exists'), $id);
	}
	/**
	 * doFindByName
	 * select an object by it's name, directly into the db.
	 *
	 * @access public
	 * @static
	 * @see Table::findOne (__call: ItemTemplate::findOneByName)
	 *
	 * @param string $n The name of the item
	 * @return Doctrine_Record the ItemTemplate instance
	 */
	public function doFindByName($n)
	{
		$this->_initItems();
		return array_search($n, $this->items);
	}
	/**
	 * doFindItemIf
	 * execute a doFindItem if ...
	 *
	 * @see doFindItem
	 * @access public
	 * @static
	 *
	 * @param integer $i The item ID
	 * @param boolean $c The condition
	 * @return Doctrine_Record|$i Doctrine_Record instance if the item exists && $c, else $i
	 */
	public function doFindItemIf($i, $c) { return $c ? $this->doFindItem($i) : $i; }
	/**
	 * doFindItemUnless
	 * execute a doFindItem UNLESS ...
	 *
	 * @see doFindItem
	 * @access public
	 * @static
	 *
	 * @param integer $i The item ID
	 * @param boolean $c The condition
	 * @return Doctrine_Record|$i Doctrine_Record instance if the item exists && !$c, else $i
	 */
	public function doFindItemUnless($i, $c) { return $this->doFindItemIf($i, !$c); }


	/** @var $templates ItemTemplate[] */
	protected $templates = array();
	public function doFindItemTemplate($id)
	{
		if ($id instanceof ShopItemEffect)
			$id = $id->value;
		if (!isset( $this->templates[$id]))
		{
			$this->templates[$id] = ItemTemplateTable::getInstance()->find($id); //fetch the record
		}
		return $this->templates[$id];
	}
	public function doFindItemTemplateIf($i, $c) { return $c ? $this->doFindItemTemplate($i) : $c; }
	public function doFindItemTemplateUnless($i, $c) { return $this->doFindItemTemplateIf($i, !$c); }
}

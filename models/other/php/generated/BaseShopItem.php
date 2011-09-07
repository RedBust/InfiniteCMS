<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('ShopItem', 'other');

/**
 * BaseShopItem
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property int $category_id
 * @property varchar $name
 * @property integer $cost
 * @property integer $cost_vip
 * @property text $description
 * @property boolean $is_vip
 * @property boolean $is_lottery
 * @property boolean $is_hidden
 * @property Doctrine_Collection $Effects
 * @property ShopCategory $Category
 * @property Doctrine_Collection $Event
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Vendethiel <vendethiel@hotmail.fr>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseShopItem extends Record
{
    public function setTableDefinition()
    {
        $this->setTableName('shop_item');
        $this->hasColumn('category_id', 'int', 9, array(
             'type' => 'int',
             'length' => '9',
             ));
        $this->hasColumn('name', 'varchar', 255, array(
             'type' => 'varchar',
             'length' => '255',
             ));
        $this->hasColumn('cost', 'integer', 9, array(
             'type' => 'integer',
             'length' => '9',
             ));
        $this->hasColumn('cost_vip', 'integer', 9, array(
             'type' => 'integer',
             'length' => '9',
             ));
        $this->hasColumn('description', 'text', null, array(
             'type' => 'text',
             ));
        $this->hasColumn('is_vip', 'boolean', null, array(
             'type' => 'boolean',
             'default' => 0,
             ));
        $this->hasColumn('is_lottery', 'boolean', null, array(
             'type' => 'boolean',
             'default' => 0,
             ));
        $this->hasColumn('is_hidden', 'boolean', null, array(
             'type' => 'boolean',
             'default' => 0,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('ShopItemEffect as Effects', array(
             'local' => 'id',
             'foreign' => 'item_id'));

        $this->hasOne('ShopCategory as Category', array(
             'local' => 'category_id',
             'foreign' => 'id'));

        $this->hasMany('Event', array(
             'local' => 'id',
             'foreign' => 'reward_id'));
    }
}
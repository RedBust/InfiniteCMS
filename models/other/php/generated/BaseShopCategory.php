<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('ShopCategory', 'other');

/**
 * BaseShopCategory
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property varchar $name
 * @property Doctrine_Collection $Items
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Vendethiel <vendethiel@hotmail.fr>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseShopCategory extends Record
{
    public function setTableDefinition()
    {
        $this->setTableName('shop_category');
        $this->hasColumn('name', 'varchar', 255, array(
             'type' => 'varchar',
             'length' => '255',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('ShopItem as Items', array(
             'local' => 'id',
             'foreign' => 'category_id'));
    }
}
<?php

/**
 * BaseTicket
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property int $category_id
 * @property enum $state
 * @property varchar $name
 * @property TicketCategory $Category
 * @property Doctrine_Collection $Answers
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Vendethiel <vendethiel@hotmail.fr>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseTicket extends Record
{
    public function setTableDefinition()
    {
        $this->setTableName('ticket');
        $this->hasColumn('category_id', 'int', 9, array(
             'type' => 'int',
             'length' => '9',
             ));
        $this->hasColumn('state', 'enum', null, array(
             'type' => 'enum',
             'values' => 
             array(
              0 => 'locked',
              1 => 'to do',
              2 => 'resolved',
              3 => 'resolving',
              4 => 'deleted',
             ),
             ));
        $this->hasColumn('name', 'varchar', 255, array(
             'type' => 'varchar',
             'length' => '255',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('TicketCategory as Category', array(
             'local' => 'category_id',
             'foreign' => 'id'));

        $this->hasMany('TicketAnswer as Answers', array(
             'local' => 'id',
             'foreign' => 'ticket_id'));
    }
}
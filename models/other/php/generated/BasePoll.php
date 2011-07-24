<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Poll', 'other');

/**
 * BasePoll
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $name
 * @property date $date_start
 * @property date $date_end
 * @property Doctrine_Collection $Options
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Vendethiel <vendethiel@hotmail.fr>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BasePoll extends Record
{
    public function setTableDefinition()
    {
        $this->setTableName('poll');
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('date_start', 'date', null, array(
             'type' => 'date',
             ));
        $this->hasColumn('date_end', 'date', null, array(
             'type' => 'date',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('PollOption as Options', array(
             'local' => 'id',
             'foreign' => 'poll_id'));
    }
}
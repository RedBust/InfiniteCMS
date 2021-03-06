<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('ContestJuror', 'other');

/**
 * BaseContestJuror
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property int $contest_id
 * @property int $user_id
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Vendethiel <vendethiel@hotmail.fr>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseContestJuror extends Record
{
    public function setTableDefinition()
    {
        $this->setTableName('contest_juror');
        $this->hasColumn('contest_id', 'int', null, array(
             'type' => 'int',
             ));
        $this->hasColumn('user_id', 'int', null, array(
             'type' => 'int',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}
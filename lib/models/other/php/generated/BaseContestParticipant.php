<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('ContestParticipant', 'other');

/**
 * BaseContestParticipant
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property int $contest_id
 * @property int $character_id
 * @property integer $votes
 * @property integer $position
 * @property Contest $Contest
 * @property Character $Character
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Vendethiel <vendethiel@hotmail.fr>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseContestParticipant extends Record
{
    public function setTableDefinition()
    {
        $this->setTableName('contest_participant');
        $this->hasColumn('contest_id', 'int', null, array(
             'type' => 'int',
             ));
        $this->hasColumn('character_id', 'int', null, array(
             'type' => 'int',
             ));
        $this->hasColumn('votes', 'integer', null, array(
             'type' => 'integer',
             'default' => 0,
             ));
        $this->hasColumn('position', 'integer', null, array(
             'type' => 'integer',
             'default' => 0,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Contest', array(
             'local' => 'contest_id',
             'foreign' => 'id'));

        $this->hasOne('Character', array(
             'local' => 'character_id',
             'foreign' => 'guid'));
    }
}
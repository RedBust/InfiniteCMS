<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Guild', 'other');

/**
 * BaseGuild
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property string $emblem
 * @property integer $lvl
 * @property integer $xp
 * @property Doctrine_Collection $Members
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Vendethiel <vendethiel@hotmail.fr>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseGuild extends Record
{
    public function setTableDefinition()
    {
        $this->setTableName('guilds');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => '4',
             ));
        $this->hasColumn('name', 'string', 50, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => '50',
             ));
        $this->hasColumn('emblem', 'string', 20, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => '20',
             ));
        $this->hasColumn('lvl', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'default' => '1',
             'notnull' => true,
             'autoincrement' => false,
             'length' => '4',
             ));
        $this->hasColumn('xp', 'integer', 8, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'default' => '0',
             'notnull' => true,
             'autoincrement' => false,
             'length' => '8',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('GuildMember as Members', array(
             'local' => 'id',
             'foreign' => 'guild'));
    }
}
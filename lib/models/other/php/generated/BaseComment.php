<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Comment', 'other');

/**
 * BaseComment
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $news_id
 * @property integer $author_id
 * @property varchar $title
 * @property text $content
 * @property News $News
 * @property User $Author
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Vendethiel <vendethiel@hotmail.fr>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseComment extends Record
{
    public function setTableDefinition()
    {
        $this->setTableName('comment');
        $this->hasColumn('news_id', 'integer', 9, array(
             'type' => 'integer',
             'length' => '9',
             ));
        $this->hasColumn('author_id', 'integer', 9, array(
             'type' => 'integer',
             'length' => '9',
             ));
        $this->hasColumn('title', 'varchar', 255, array(
             'type' => 'varchar',
             'length' => '255',
             ));
        $this->hasColumn('content', 'text', null, array(
             'type' => 'text',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('News', array(
             'local' => 'news_id',
             'foreign' => 'id'));

        $this->hasOne('User as Author', array(
             'local' => 'author_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             'updated' => 
             array(
              'disabled' => true,
             ),
             ));
        $this->actAs($timestampable0);
    }
}
<?php

/**
 * Subarea
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: AreaData.php 20 2010-09-24 08:51:22Z nami.d0c.0 $
 */
class Subarea extends BaseSubarea
{
	public function setUp()
	{
		$this->hasOne( 'Area', array(
				'local' => 'area',
				'foreign' => 'id',
			) );
	}
}
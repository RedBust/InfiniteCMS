<?php

/**
 * adds more possibility to the basic Doctrine_Table class
 *
 * @file $Id: RecordTable.php 20 2010-09-24 08:51:22Z nami.d0c.0 $
 *
 * @extends Doctrine_Table
 */
class RecordTable extends Doctrine_Table
{

	public function lasts($limit)
	{
		$limit = intval($limit);
		return Query::create()
				->from($this->getClassnameToReturn())
				->limit($limit);
	}

	static public function getInstance()
	{
		return Doctrine_Core::getTable(substr(get_called_class(), 0, -5));
	}
}
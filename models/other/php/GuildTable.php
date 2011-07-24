<?php

/**
 * GuildTable
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-Doc <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: GuildTable.php 24 2010-10-22 11:46:07Z nami.d0c.0 $
 */
class GuildTable extends RecordTable
{
	protected $_emblemsInit = false;

	public function initEmblems()
	{
		if ($this->_emblemsInit)
			return;
		$this->_emblemsInit = true;
		jQ('var emblems = {};');
	}
}
<?php

/**
 * UserTable
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-Doc <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: CompteTable.php 24 2010-10-22 11:46:07Z nami.d0c.0 $
 */
class UserTable extends RecordTable
{
	public function fromGuid($guid, $load = true)
	{
		static $users = array();
		if ($guid instanceof Account || is_array($guid))
			$guid = $guid['guid'];
		if (isset($users[$guid]))
			return $users[$guid];

		if (!$u = $this->findOneByGuid($guid))
		{
			$u = new User;
			$u->guid = $guid;
			$u->save();
		}
		$users[$u['guid']] = $u;
		return $u;
	}
}
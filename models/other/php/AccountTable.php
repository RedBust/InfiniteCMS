<?php

/**
 * AccountTable
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-Doc <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: AccountTable.php 24 2010-10-22 11:46:07Z nami.d0c.0 $
 */
class AccountTable extends RecordTable
{
	public function findReverseFriends($guid = null)
	{ //todo move this to Account class ...
		if (null === $guid)
		{
			global $account;
			$guid = $account->guid;
		}
		return $this->createQuery()
					->where('friends LIKE ? OR friends LIKE ? OR friends = ?', array($guid . ';%', '%;' . $guid . ';%', $guid));
	}
}
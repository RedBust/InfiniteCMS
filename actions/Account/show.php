<?php
/* @var $account Account */
if (isset($accountId))
	$fromInclude = true; //from include (i.e. the modal box)
else
{
	$fromInclude = false;
	$accountId = $router->requestVar('id', -1);
}

if (!( $acc = AccountTable::getInstance()->createQuery('a')
				->leftJoin('a.User u')
				->leftJoin('a.Characters c')
				->where('a.guid = ?', $accountId)
		->fetchOne() ))
{ //@todo better error handling with $fromInclude ?
	echo lang('acc.does_not_exists');
	exit('lol');
	return;
}
/* @var $acc Account */
$nl = tag('br');
if (!empty($acc->lastconnectiondate))
{
	$lastCnx = explode('~', $acc->lastconnectiondate);
	$lastCnx = sprintf(lang('created'), ( count($lastCnx) > 1 ) ? sprintf('%d-%d-%d %d:%d:00', $lastCnx[0], $lastCnx[1], $lastCnx[2], $lastCnx[3], $lastCnx[4]) : $lastCnx[0]);
}
else
	$lastCnx = NULL;
global $account; //:(
$isFriend = false;
$isFriendR = false;
if ($connected)
{ //explanations below
	$isFriend = $account->hasFriend($acc);
	$isFriendR = $acc->hasFriend($account);
}

/*
 * cases:
 *  1) $friend && $friendR	=> both are friends
 *  2) $friend				=> me is friend but not viewed
 *  3) $friendR				=> viewed is friend but not viewer
 *  4) none					=> both are not friends
 *
 * (ok, I admit that this passage is pretty ugly (and PHP is slow on ternary conditions) ...
 */
$isAdmin = level(LEVEL_ADMIN);
if (!$acc->relatedExists('User'))
	$acc->User = UserTable::getInstance()->fromGuid($acc->guid);
$info = tag('b', lang('pseudo') . ': ') . $acc->getName() . $nl .
		$acc->getLevel() . $nl .
		( $isAdmin ? ($acc ? $acc->User->getPoints() : 0) . $nl : '' ) . //points if admin?
		( $acc->banned ? sprintf(lang('acc.is_banned'), $acc->pseudo) . $nl : '' ) . //banned?
		( $isFriend && $isFriendR ? sprintf(lang('acc.in_ur_friends_&'), $acc->getName()) : //parse
				( $isFriend ? sprintf(lang('acc.in_ur_friends'), $acc->getName()) : ( $isFriendR ? //friend
								sprintf(lang('acc.in_ur_friends_!'), $acc->getName()) : '' ) ) ) . $nl . //(or not)
		( empty($lastCnx) ? '' : tag('b', lang('acc.last_cnx') . ': ') . $lastCnx ) . //lastConnection
		( $isAdmin ? $nl . make_link(array(//edit link
				'controller' => 'User',
				'action' => 'update',
				'id' => $acc->guid,
			), sprintf(lang('acc.edit'), $acc->pseudo), array(), array('class' => 'link')) . '&nbsp;&bull;&nbsp;' . make_link(array(
				'controller' => 'Account',
				'action' => 'update',
				'id' => $acc->guid,
			), sprintf(lang('acc.edit_infos'), $acc->pseudo), array(), array('class' => 'link')) : '' );

unset($accountId);
if ($fromInclude)
{
	$info .= $nl . make_link(array('controller' => 'Account', 'action' => 'show', 'id' => $acc->guid),
					lang('more') . ' ...', array(), array(), true);
	return $info;
}
else
{ //full page
	echo tag('div', $info);
	// parse friends
	$friends = '';
	foreach ($acc->getFriends() as $f)
	{
		/* @var $f Account */
		if ($f->guid != $acc->guid)
		{ //not himself his friend (yeah, sometimes it happens ... sing with me ... "I'm a poor lonesone dofus player" ...)
			$friends .= tag('li', '&bull;&nbsp;' . make_link($f));
		}
	}

	if ($friends !== '')
		echo tag('h2', pluralize(lang('acc.friend'), count($acc->getFriends()))) .
		 tag('ul', $friends);
	if ($acc->relatedExists('Characters') && $acc->Characters->count())
	{ //show characters
		echo tag('h2', ucfirst(pluralize(lang('character'), $acc->Characters->count()))),
		$acc->Characters->charactersDisplay(array(//the default params
			'controller' => $router->getController(),
			'action' => $router->getAction(),
			'id' => $acc->guid,
		));
	}
} //end else $fromInclude
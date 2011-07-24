<?php
if (!check_level(LEVEL_LOGGED))
	return;

$reverseF = AccountTable::getInstance()
						->findReverseFriends();
if (isset($_GET['q']))
	$reverseF->having('LOWER(pseudo) LIKE ?', str_replace('%', '', strtolower($_GET['q'])) . '%');
$reverseF = $reverseF->fetchArray();

$returns = array();
foreach ($reverseF as $friend)
{
	$returns[] = array('id' => $friend['guid'], 'name' => $friend['pseudo']);
}
echo json_encode($returns);
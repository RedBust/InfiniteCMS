<?php
if (!check_level(LEVEL_LOGGED))
	return;

$reverseF = $account->getReverseFriendsQ();
if (isset($_GET['q']))
	$reverseF->having('LOWER(pseudo) LIKE ?', str_replace('%', '', strtolower($_GET['q'])) . '%');
$reverseF = $reverseF->fetchArray();

$returns = array();
foreach ($reverseF as $friend)
{ //can't use toValueArray koz id => guid
	$returns[] = array('id' => $friend['guid'], 'name' => $friend['pseudo']);
}
echo json_encode($returns);
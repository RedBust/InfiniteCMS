<?php
if (!check_level(LEVEL_LOGGED))
	return;

$reverseFriends = $account->getReverseFriends();
$list = array();
if (isset($_GET['q']))
{
	$q = strtolower($_GET['q']);
	foreach ($reverseFriends as $friend)
	{
		if (strpos(strtolower($friend->pseudo), $q) !== false)
			$list[] = $friend; 
	}
}
$returns = array();
foreach ($list as $friend)
{ //can't use toValueArray koz id => guid
	$returns[] = array('id' => $friend['guid'], 'name' => $friend['pseudo']);
}
echo json_encode($returns);
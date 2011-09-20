<?php
if (!check_level(LEVEL_LOGGED))
	return;

$reverseF = $account->getReverseFriends();
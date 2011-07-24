<?php
if (!check_level(LEVEL_LOGGED))
	return;

$reverseF = AccountTable::getInstance()
						->findReverseFriends()
						->fetchArray();
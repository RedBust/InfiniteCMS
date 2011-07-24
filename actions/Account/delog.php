<?php
if (!check_level(LEVEL_LOGGED))
	return;

$member->disconnect();
redirect('@root');
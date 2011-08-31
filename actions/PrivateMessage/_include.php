<?php
if (!check_level(LEVEL_LOGGED))
	return;

if ($router->isAjax())
{
	define('IN_PM', true);
	define('UPDATE_SELECTOR', '#pm');
	jQ('$("#pm").dialog("open");');
}
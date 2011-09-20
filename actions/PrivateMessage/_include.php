<?php
if (!check_level(LEVEL_LOGGED))
	return;

define('IN_PM', true);
if ($router->isAjax())
{ //for some reason, tokenInput in a dialog does not work. Screw that ...
#	define('UPDATE_SELECTOR', '#pm');
#	jQ('$("#pm").dialog("open");');
}
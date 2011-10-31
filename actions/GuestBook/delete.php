<?php
if (!check_level(LEVEL_ADMIN))
	return;

$id = $router->requestVar('id', -1);
$router->codeUnless(404, $rate = ReviewTable::getInstance()->find($id));

$rate->delete();
printf(lang('rate.deleted'), $id);
<?php
if (!check_level(LEVEL_ADMIN))
	return;

$id = $router->requestVar('id', -1);
if(!( $rate = ReviewTable::getInstance()->find($id) ))
{
	printf(lang('rate.not_found'), $id);
	return;
}

$rate->delete();
printf(lang('rate.deleted'), $id);
<?php
if (!check_level(LEVEL_LOGGED))
	return;
if (!check_level(LEVEL_VIP, REQUIRE_NOT))
	return;
if (empty($config['COST_VIP']) || $config['COST_VIP'] > $account->User->points)
{
	define('HTTP_CODE', 404);
	return;
}
$account->User->points -= $config['COST_VIP'];
$account->vip = 1;
echo lang('acc.vip!');
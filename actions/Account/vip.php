<?php
if (!check_level(LEVEL_LOGGED))
	return;
if (!check_level(LEVEL_VIP, REQUIRE_NOT))
	return;
$router->codeIf(404, empty($config['COST_VIP']) || $config['COST_VIP'] > $account->User->points);

$account->User->points -= $config['COST_VIP'];
$account->vip = 1;
echo lang('acc.vip!');
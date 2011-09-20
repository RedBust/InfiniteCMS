<?php
if (!check_level(LEVEL_ADMIN))
	return;

if (!$role = StaffRoleTable::getInstance($id = $router->requestVar('id')))
{
	define('HTTP_CODE', 404);
	return;
}

$role->delete();
redirect(StaffRoleTable::getInstance());
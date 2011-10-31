<?php
if (!check_level(LEVEL_ADMIN))
	return;

$router->codeUnless(404, $role = StaffRoleTable::getInstance($id = $router->requestVar('id')));

$role->delete();
redirect(StaffRoleTable::getInstance());
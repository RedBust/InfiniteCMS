<?php
$table = StaffRoleTable::getInstance();
$staffMembers = Query::create()
				->from('Account a')
					->leftJoin('a.StaffRoles sr')
				->where('a.level > 0')
				->execute();

$hasStaff = false;
if ($staffMembers->count())
{
	echo '
<table>';
	foreach ($staffMembers as $sm)
	{ //right, the name is only for "sm" joke. I'm so tired :(
		if (!level(LEVEL_ADMIN) && !$sm->StaffRoles->count())
			continue;
		$hasStaff = true;

		echo tag('tr', tag('td', make_link($sm)) . tag('td', $sm->getRolesString()));
	}
	echo '
</table>';
}
if (!$hasStaff)
	echo lang('staff_empty');
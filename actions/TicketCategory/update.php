<?php
if (!check_level(LEVEL_ADMIN))
	return;

if (!( $category = TicketCategoryTable::getInstance()->find($id = $router->requestVar('id')) ))
	$category = new TicketCategory;

if (!empty($_POST))
{
	$col = $router->requestVar('col', '');
	$vals = $_POST;
	if (!$category->getTable()->hasColumn($col) || empty($_POST['update_value']))
		$col = array();
	else //replace $vals
		$vals = array($col => $_POST['update_value']);
	$errors = $category->update_attributes($vals, $col);	
	if (!empty($col) && !is_array($col))
		exit(nl2br($category[$col]));
}
if (empty($_POST) || $errors != array())
{
	partial('_form', array('category'), PARTIAL_CONTROLLER);
}
elseif (!empty($_POST) && $errors == array() && $headers)
{
#	echo lang('ticketcategory.saved') . make_link('@root', lang('back_to_index'));
	redirect(array('controller' => $router->getController(), 'action' => 'show', 'id' => $category['id']));
}
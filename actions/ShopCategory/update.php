<?php
if (!check_level(LEVEL_ADMIN))
	return;

if (!$category = ShopCategoryTable::getInstance()->find($c = intval($router->requestVar('id'))))
{
	$category = new ShopCategory;
	$title = lang($router->getController() . ' - create', 'title');
}

if ($sent = ( count($_POST) > 0 ))
{
	if (!empty($_POST['name']))
		$category->name = $_POST['name'];
	else
		$errors[] = sprintf(lang('must_!empty'), 'name');

	if (empty($errors))
		$category->save();
}
if (!$sent || $errors != array())
	partial('_form', array('category'), PARTIAL_CONTROLLER);
elseif ($sent && $errors === array())
	redirect('@shop');
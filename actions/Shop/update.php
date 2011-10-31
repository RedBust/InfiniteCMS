<?php
if (!check_level(LEVEL_ADMIN))
	return;

load_models('static');

if (!$item = Query::create()
					->from('ShopItem i')
						->leftJoin('i.Effects e INDEXBY e.id')
					->where('i.id = ?', $router->requestVar('id', -1))
					->fetchOne())
{
	$item = new ShopItem;
	$title = lang($router->getController() . ' - create', 'title');
}

if ($sent = ( count($_POST) > 0 ))
{
	$col = $router->requestVar('col', '');
	$vals = $_POST;
	if (!$item->getTable()->hasColumn($col) || !isset($_POST['update_value']))
		$col = NULL;
	else
		$vals = array($col => $_POST['update_value']);
	$errors = $item->update_attributes($vals, $col);

	if ($col !== NULL)
		exit(nl2br($item[$col]));
}

if (!$sent || $errors != array())
	partial('_form', array('item', 'types', 'config', 'jQ'), PARTIAL_CONTROLLER);
elseif ($sent && $errors === array())
{
	echo lang('shop.item.saved');
	redirect('@shop');
}
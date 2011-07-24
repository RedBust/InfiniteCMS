<?php
if (!check_level(LEVEL_ADMIN))
	return;

load_models('static');

if (!( $objet = Query::create()
					->from('ShopItem i')
						->leftJoin('i.Effects e INDEXBY e.id')
					->where('i.id = ?', $router->requestVar('id', -1))
					->fetchOne() ))
{
	$objet = new ShopItem;
	$title = lang($router->getController() . ' - new', 'title');
}

$sent = count($_POST) > 0;
if ($sent)
{
	$col = $router->requestVar('col', '');
	$vals = $_POST;
	if (!$objet->getTable()->hasColumn($col) || !isset($_POST['update_value']))
		$col = NULL;
	else
		$vals = array($col => $_POST['update_value']);
	$errors = $objet->update_attributes($vals, $col);
	if ($col !== NULL)
	{
		exit(nl2br($objet[$col]));
	}
}

if (!$sent || $errors != array())
	partial('_form', array('objet', 'types', 'config', 'jQ'), PARTIAL_CONTROLLER);
elseif ($sent && $errors === array())
{
	echo lang('shop.item.saved');
	redirect('@shop');
}
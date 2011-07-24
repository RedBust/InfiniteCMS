<?php
if (!check_level(LEVEL_ADMIN))
	return;

$mode = $router->requestVar('mode', 'Item');
if ($mode !== 'Item' && $mode !== 'ItemEffect')
{ //not good :p
	define('HTTP_CODE', 404);
	return;
}
$item = Doctrine_Core::getTable('Shop' . $mode)->find($id = $router->requestVar('id', -1));

if (!$item)
{
	printf(lang('shop.item_not_exists'), html($id));
	return;
}
if ($mode == 'Item')
	Query::create()
		->delete('ShopItemEffect')
			->where('item_id = ?', $item->id)
		->execute();
else
	$itemID = $item->Item->id;
$item->delete();
echo lang('shop.item.deleted');

if ($mode == 'ItemEffect')
	redirect(array('controller' => $router->getController(), 'action' => 'edit', 'id' => $itemID)); //redirect to the "edit" page
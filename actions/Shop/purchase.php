<?php
$id = $router->requestVar('id', -1);
$return_back = '<br /><br />' . make_link('@shop', lang('back_to_index'));
$admin = level( LEVEL_ADMIN );

if (!( $shopItem = Query::create()
		->select('i.cost, e.*')
			->from('ShopItem i')
				->leftJoin('i.Effects e')
			->where('id = ?', $id)
		->fetchOne() ))
{
	printf(lang('shop.item.not_exists'), intval($id), $return_back);
	return;
}

$char = $account->getMainChar();
if (!$char)
{
	define('HTTP_CODE', 404);
	return;
}
/* @var $char Character */

if (!$admin)
{
	if ($shopItem->cost > $account->User->points)
	{
		printf(lang('shop.cannot_buy_but_credit'), $shopItem->name,
		 make_link('@vote', lang('acc.vote')), make_link('@credit', lang('acc.credit.add')), $return_back);
		return;
	}
	if ($shopItem->is_hidden
	 || ($shopItem->is_vip && !$account->vip))
	{
		define('HTTP_CODE', 404);
		return;
	}
}
if ($shopItem->is_lottery && $shopItem->Effects->count() > 1)
{
	$effect = $shopItem->Effects[rand(0, $shopItem->Effects->count())];
	$char->give($effect);

	printf(lang('shop.bought'),	$shopItem['name'], $effect, $return_back);
}
else
{
	foreach ($shopItem->Effects as $effect)
		$char->give($effect);

	printf(lang('shop.bought'), $shopItem['name'], $return_back);
}

if(!$admin) //so yeah, if you're admin your points won't decrease ... That's not a bug, that's a feature ...
	$account->User['points'] -= $shopItem['cost'];

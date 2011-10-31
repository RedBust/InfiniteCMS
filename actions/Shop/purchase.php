<?php
$id = $router->requestVar('id', -1);
$admin = level( LEVEL_ADMIN );

$router->codeUnless(404, $shopItem = Query::create()
		->select('i.cost, e.*')
			->from('ShopItem i')
				->leftJoin('i.Effects e')
			->where('id = ?', $id)
		->fetchOne());
$return_back = '<br />' . make_link(array('controller' => 'Shop', 'action' => 'index', 'cat' => $shopItem->category_id), lang('back_to_index'));

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
	 || ($shopItem->is_vip && (empty($config['COST_VIP']) || !$account->vip)))
	{
		define('HTTP_CODE', 404);
		return;
	}
}
$effect = $shopItem->giveTo($char);
if ($shopItem->is_lottery && $shopItem->Effects->count() > 1)
{
	printf(lang('shop.lottery_bought'),	$shopItem['name'], str_replace(tag('br'), '', $effect));
}
else
{
	printf(lang('shop.bought'), $shopItem['name']);
}

if($admin) //so yeah, if you're admin your points won't decrease ... That's not a bug, that's a feature ...
	echo tag('br') . tag('br') . lang('shop.undecredited_because_admin');
else
	$account->User['points'] -= $shopItem['cost'];

echo tag('br') . $return_back;
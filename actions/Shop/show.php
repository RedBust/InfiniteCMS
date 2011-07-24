<?php
$id = $router->requestVar( 'id', -1 );
$perso = $router->requestVar( 'perso' );
$return_back = '<br /><br />' . make_link('@shop', lang( 'back_to_index' ) );
$admin = level( LEVEL_ADMIN );
$force = $router->requestVar('force', 0) == 1 && $admin;

if (!( $objet = Query::create()
		->select('i.cost, e.*')
			->from('ShopItem i')
				->leftJoin('i.Effects e')
			->where('id = ?', $id)
		->fetchOne() ))
{
	printf(lang('shop.item.not_exists'), intval($id), $return_back);
	return;
}

if ($perso === NULL)
{
	echo tag('h1', pluralize(lang('character'), $account->Characters->count(), false));
	foreach ($account->Characters as $character)
	{
		echo $character . tag('br')
		 . make_link(array('controller' => $router->getController(), 'action' => 'show', 'id' => $id, 'perso' => $character->guid), lang('choose'));
	}
}
else
{
	$choosedPerso = CharacterTable::getInstance()->find($perso);
	if (!$choosedPerso || ( $choosedPerso && $choosedPerso->Account !== $account && !$force ) ) //you've maybe noticed it's possible to give a item to a man ... yeah :p
		echo lang('shop.!character_on_acc'), $return_back;
	/* @var $choosedPerso character */

	if ($objet['cost'] > $account->User['points'])
	{ //@todo and if credit & vote are disabled ?!
		if ($admin)
		{
			echo lang('shop.uncredited_because_admin'); //"undecredited" in fact. PARDON MY FRENCH
		}
		else
		{
			printf(lang('shop.cannot_buy_but_credit'), $objet['name'],
			 make_link('@vote', lang('acc.vote')), make_link('@credit', lang('acc.credit.add')), $return_back);
			return;
		}
	}
	foreach( $objet->Effects as $effect )
		$choosedPerso->give( $effect );
	if(!$admin) //so yeah, if you're admin your points won't decrease ... That's not a bug, that's a feature ...
		$account->User['points'] -= $objet['cost'];

	printf(lang('shop.bought'),
	 $objet['name'], $return_back);
}
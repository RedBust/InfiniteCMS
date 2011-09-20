<?php
if (!check_level(LEVEL_ADMIN))
	return;

if (!($character = CharacterTable::getInstance()->find($id = $router->requestVar('id', -1))))
{
	echo lang('character.does_not_exists');
	return;
}
/* @var $character Character*/

$effect = new ShopItemEffect;

$type = $router->requestVar('type', -1);
if (!in_array($type, array_keys($types)))
{
	echo lang('incorrect_type');
	return;
}
$effect->type = $type;

if (!$effect->setValue($router->requestVar('value')))
{
	echo lang('incorrect_value');
	return;
}

load_models('static');

$value = $effect->getValue();
if ($value instanceof ItemTemplate)
	$value = lang($value->id, 'item');

$effect->giveTo($character);
unset($effect);
printf(lang('character.given'), $types[$type], $value, make_link($character));
<?php
defined('FROM_INCLUDE') || define('FROM_INCLUDE', false);
$table = ShopItemTable::getInstance();
/* @var $table ItemTable */
if (!$objet->exists())
	$title = lang('Shop - new', 'title');
/* @var $objet Item */
$preNl = FROM_INCLUDE ? '' : '<br />';
$loc = FROM_INCLUDE ? to_url(array(
			'controller' => 'Shop',
			'action' => 'edit',
			'id' => $objet['id'],
		)) : APPEND_FORM_TAG;
$champs = array(
	array('name', lang('name') . '<br />', NULL, $objet->name),
	array('cost', $preNl . sprintf(lang('shop.cost'), $config['POINTS_CREDIT'], $config['POINTS_VOTE']) . '<br />', NULL, $objet->cost),
	array('description', $preNl . lang('content') . '<br />', 'textarea', $objet->description),
);
/* array( 'type', $preNl . lang( 'action' ) . '<br />', 'select', $types, $objet->type ),
  array( 'value', $preNl . lang( 'value' ) . '<br />', NULL, $objet->getValue() ),
 */
$lang_act = lang('action');
$lang_val = lang('value');
$ef = array();
$id = 0;
$_types = array(-1 => strip_tags(lang('no_value'))) + $types;

foreach ($objet->Effects as $effect)
{ /* @var $effect ItemEffect */
	if ($effect->type === NULL || $effect->type == -1) //wtf
		continue;

	$ef[] = array('type[' . $effect->id . ']', $lang_act . ' :', 'select', $_types, $effect->type);
	$ef[] = array('value[' . $effect->id . ']', '&nbsp;&bull;&nbsp;' . $lang_val . ' :', NULL, $effect->getValue() . '');
	$ef[] = make_link(array('controller' => $router->getController(), 'action' => 'delete', 'mode' => 'ItemEffect', 'id' => $effect->id), lang('act.delete')) . '<hr />';
	$id = $effect->id + 1;
}
$ef[] = array('type[' . $id . ']', $lang_act . ' :', 'select', $_types);
$ef[] = array('value[' . $id . ']', '&nbsp;&bull;&nbsp;' . $lang_val . ' :');

$champs[lang('effects')] = $ef;
jQ(sprintf('
var idEffect = %d,
	addEffect = $("<span />", { "id": "addEffect" })
	 .html("[<b>+</b>]");
var inputs = $( ".slideMenu[name=\'%s\']" )
	.before(addEffect)
	.parent()
	.next();
addEffect
	.click(function ()
	{
		idEffect++;
		inputs
			.append( $( "<hr />" ) )
			.append( $( "<div />", { "id": "effect_" + idEffect } )
			 .append( $( "<label />", { "for": "type[" + idEffect + "]" } )
			  .html( %s + " :" ) )
			 .append( $( "<select />", { "name": "type[" + idEffect + "]" } )
			  .html( ' . javascript_val(input_select_options($_types)) . ' ) ) //DAT IS SO MAGIC §§§ InfiniteCMS rulz, becoz I\'m ceilcat and becoz ceilcat invnt\'d buttsex. o hai
			 .append( "&nbsp;&bull;&nbsp;" )
			 .append( $( "<label />", { "for": "value[" + idEffect + "]" } )
			  .html( %s + " :" ) )
			 .append( $( "<input />", { "type": "text", "name": "value[" + idEffect + "]" } ) ) );
	} );
binds.add(function ()
	{
		delete idEffect;
		delete addEffect;
		delete inputs;
	});', $id, javascript_string(lang('effects'), "'"),
				javascript_val($lang_act), javascript_val($lang_val)));
$code = ( FROM_INCLUDE ? '' : tag('h1', lang('shop.new')) . '<br />' ) . make_form($champs, $loc);
$table->getAutoComplete();

if (FROM_INCLUDE)
	return $code;
else //return != echo
	echo $code;
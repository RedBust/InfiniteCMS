<?php
defined('FROM_INCLUDE') || define('FROM_INCLUDE', false);
$table = ShopItemTable::getInstance();
/* @var $table ItemTable */
/* @var $item Item */
$preNl = FROM_INCLUDE ? '' : '<br />';
$loc = FROM_INCLUDE ? to_url(array(
			'controller' => 'Shop',
			'action' => 'update',
			'id' => $item['id'],
		)) : '#';
$fields = array(
	array('name', lang('name') . '<br />', NULL, $item->name),
	array('cost', $preNl . sprintf(lang('shop.cost'), $config['POINTS_CREDIT'], $config['POINTS_VOTE'], $config['POINTS_CREDIT_VIP'], $config['POINTS_VOTE_VIP']) . '<br />', NULL, $item->cost),
	array('cost_vip', $preNl . lang('cost_vip') . '<br />', NULL, $item->cost_vip),
	array('description', $preNl . lang('content') . '<br />', 'textarea', $item->description),
	tag('div', array('id' => 'options'), 
	 input('is_vip', lang('shop.is_vip'), 'checkbox', $item->is_vip) .
	 input('is_lottery', lang('shop.is_lottery'), 'checkbox', $item->is_vip) .
	 input('is_hidden', lang('shop.is_hidden'), 'checkbox', $item->is_vip) . tag('br')
	 ),
);
/* array( 'type', $preNl . lang( 'action' ) . '<br />', 'select', $types, $item->type ),
  array( 'value', $preNl . lang( 'value' ) . '<br />', NULL, $item->getValue() ),
 */
$lang_act = lang('action');
$lang_val = lang('value');
$ef = array();
$id = 0;
$_types = array(-1 => strip_tags(lang('no_value'))) + $types;

foreach ($item->Effects as $effect)
{ /* @var $effect ItemEffect */
	if ($effect->type === NULL || $effect->type == -1) //wtf
		continue;

	$ef[] = array('type[' . $effect->id . ']', $lang_act . ' :', 'select', $_types, $effect->type);
	$ef[] = array('value[' . $effect->id . ']', '&nbsp;&bull;&nbsp;' . $lang_val . ' :', NULL, $effect->getValue() . '');
	$ef[] = $effect->getDeleteLink() . '<hr />';
	$id = $effect->id + 1;
}
$ef[] = array('type[' . $id . ']', $lang_act . ' :', 'select', $_types);
$ef[] = array('value[' . $id . ']', '&nbsp;&bull;&nbsp;' . $lang_val . ' :');

$fields[lang('effects')] = $ef;
jQ(sprintf('
$("#options").buttonset();
var cost_couple = $("#form_couple_cost"),
	cost_on = false;
$("#form_is_vip").change(function ()
{ //cost_on = workaround
	cost_on = !cost_on;
	if (cost_on)
		cost_couple.hide();
	else
		cost_couple.show();
});

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
		if ($("option[value=-1]:selected").length)
		{
			alert(' . javascript_val(lang('shop.fill_before_add')) . ');
			return false;
		}

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
$code = ( FROM_INCLUDE ? '' : tag('h1', lang('shop.new')) . '<br />' ) . make_form($fields, $loc);

if (FROM_INCLUDE)
	return $code;
else //return != echo
	echo $code;
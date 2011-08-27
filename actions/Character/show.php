<?php
load_models('static'); //Map
$table = CharacterTable::getInstance();
/* @var $table CharacterTable */

if (!$charac = $table->retrieve())
{
	echo lang('character.does_not_exists');
	return;
}
/* @var $charac Character */
$acc = $charac->Account;
/* @var $acc Account */

if ($acc == $account) //it's YOUR account
	printf(lang('character.on_acc'), $charac->name);
else
	printf(lang('character.on_acc_of'), $charac->name, make_link($acc));

$items = $charac->getItems(); //items info
if (empty($items)) //no items (:°)
{
	$itemCount = sprintf(lang('character.item_no'), $charac->name);
	$characSep = 1; //number of <br />
}
else
{
	$itemCount = sprintf(lang('has'), $charac->name, count($items),
					pluralize(lang('character.item'), count($items)));

	//armury:
	$itemsInfo = array(); //stock all stats of equiped items

	$posOffset = array(
		0 => 'width:40px; height:40px; margin:42px 0 0 220px;',
		1 => 'width:56px; height:56px; margin:24px 0 0 317px;',
		2 => 'width:40px; height:40px; margin:105px 0 0 119px;',
		3 => 'width:55px; height:55px; margin:102px 0 0 212px',
		4 => 'width:40px; height:40px; margin:105px 0 0 327px',
		5 => 'width:55px; height:55px; margin:198px 0 0 212px;',
		6 => 'width:48px; height:48px; margin:24px 0 0 421px;',
		7 => 'width:48px; height:48px; margin:89px 0 0 421px;',
		8 => 'width:48px; height:48px; margin:153px 0 0 421px;',
		9 => 'width:31px; height:31px; margin:19px 0 0 27px;',
		10 => 'width:31px; height:31px; margin:61px 0 0 27px;',
		11 => 'width:31px; height:31px; margin:104px 0 0 27px;',
		12 => 'width:31px; height:31px; margin:147px 0 0 27px;',
		13 => 'width:31px; height:31px; margin:189px 0 0 27px;',
		14 => 'width:31px; height:31px; margin:231px 0 0 27px;',
		15 => 'width:56px; height:56px; margin:24px 0 0 112px;',
		'dinde' => 'width:48px;height:48px;margin:218px 0 0 421px;',
		'kamas' => 'width:120px; height:22px; color:#f5f4f2; margin:265px 0 0 76px; text-align:center; font:12px Verdana; font-weight:bold;',
	);
	$pos = array_keys($posOffset);
	foreach ($items as $item)
	{ //put in a array all items equiped, by the pos.
		/* @var $item Items */
		if (in_array($item->pos, $pos))
		{
			$itemsInfo[$item->pos] = array($item, IG::parseStats($item->stats));
		}
	}
	$stuff = tag_open('div', array(
				'id' => 'stuff',
			)) . '>' . make_img('stuff', EXT_PNG, array(
				'style' => 'position: absolute;',
			)); //opening of the tag ...
	foreach ($itemsInfo as $itemInfo)
	{ //$itemInfo = [instanceof Items, stats]
		/* @var $item Items */
		$item = $itemInfo[0];

		$stuff .= tag('div', array(
					'id' => 'item' . $item->template,
						'class' => 'showEffects',
					'title' => str_replace('"', "'", $itemInfo[1]), //HERE is the problem. What if JS is disabled :/ ?
					'style' => 'position: absolute; ' . $posOffset[$item->pos],
				), make_img('items/' . $item->template, EXT_PNG, array(
						'style' => 'width: 50px; height: 50px;',
					)));
	}
	echo $stuff, str_repeat(tag('br'), 8), '</div>';
	IG::registerEffectsTooltip(); //hover tooltips with item effects
	$characSep = 6; //see below
}

$tableStats = ''; //HTML Code
$stats = array(
	array('vitality', 'vitalite', '7d'),
	array('wisdom', 'sagesse', '7c'),
	array('strength', 'force', '76'),
	array('agility', 'agilite', '77'),
	array('chance', NULL, '7b'),
	array('intell', 'intelligence', '7e'),
);
foreach ($stats as $stat)
{
	$name = $stat[0]; //english name
	$value = $charac[$stat[1] === NULL ? $name : $stat[1]];
	$add_value = IG::getStat($stat[2]);
	$add = $add_value ? ' (' . IG::statFromCode($add_value) . ')' : '';
	$base_value = ( $base_value = IG::statFromCode($value) ) === NULL ?
			'<span style="color: orange;">0</span>' : $base_value;
	$tableStats .= tag('tr', tag('td', tag('b', lang('shop.stat.' . $name))) .
					tag('td', '&nbsp;&nbsp;' . $base_value . $add));
}
echo str_repeat(tag('br'), $characSep), $charac->toString(false), tag('br'), lang('character.characteristics'), ' :',
 tag('table', array('style' => 'margin-left: 10px;'), $tableStats), tag('br'), $itemCount;
$spellCount = $charac->getSpellCount();
$spellTitle = pluralize(lang('character.spell'), $spellCount);
$align = array(//HTML attributes
	'align' => 'center',
	'valign' => 'middle',
);
//spell list
echo tag('br'), sprintf(substr(lang('has'), 0, -1), $charac->name, $spellCount, tag('span', array('id' => 'openSpellsTable'), $spellTitle)),
 '<span class="showThis">.</span> <span class="hideThis">:</span>' . tag('br') . tag('br') . '<div id="spellsBox" title="' . ucfirst($spellTitle) . '">
	<table border="1" style="width: 100%;"><thead>' . tag('tr',
		tag('td', $align + array('style' => 'width: 42%;'), tag('b', lang('character.spell_name'))) .
		tag('td', $align + array('style' => 'width: 23%;'), tag('b', lang('character.spell_level'))) .
		tag('td', $align + array('style' => 'width: 35%;'), tag('b', lang('character.spell_pos')))) . '</thead><tbody style="height: 550px; overflow: auto;">';

foreach ($charac->getSpells() as $spell)
{ //build spell row
	echo tag('tr', array('data-id' => $spell[0]),
			tag('td', lang($spell[0], 'spell')) .
			tag('td', $spell[1]) .
			tag('td', $charac->getSpellPos($spell[2])));
}
echo '
		</tbody>
	</table>
</div>';

if (!empty($charac->GuildMember))
{ //show the guild info
	printf(lang('character.guild_infos'),
			$charac->name, make_link($charac->GuildMember->Guild), $charac->GuildMember->Guild->lvl);
}
if (level(LEVEL_MJ) && $map = $charac->getMap())
{ //show the mapinfo
	/* @var $map Map */
	printf(lang('character.pos'), $charac->name, $charac->map, $map->getPosX(), $map->getPosY(), $charac->cell);
}

if (level(LEVEL_ADMIN))
{
	echo tag('h3', lang('character.give')),
	 make_form(array(
		 array('type', lang('action') . '&nbsp;', 'select', $types),
		 array('value', lang('value') . '&nbsp;'),
		 array('id', null, 'hidden', ''.$charac->guid),
	 ), '@character.give');
}

jQ('
var spellsBox = $( "#spellsBox" );
spellsBox
	.hide()
	.dialog( $.extend( dialogOpt, { modal: false } ) );
$( "#openSpellsTable" ).click( function ()
	{
		spellsBox.dialog( "open" );
	} ).wrap( "<u></u>" );
binds.add(function ()
	{
		delete spellsBox;
	});');
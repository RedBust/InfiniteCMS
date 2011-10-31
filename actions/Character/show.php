<?php
load_models('static'); //Map
$table = CharacterTable::getInstance();
/* @var $table CharacterTable */

$router->codeUnless(404, $charac = $table->retrieve());
/* @var $charac Character */
$router->codeUnless(404, $acc = $charac->Account); //if it's possible =°
/* @var $acc Account */

$items = $charac->getItems(); //items info
if (count($items))
	$itemsInfo = $items->process();
$evC = $charac->Events->count();
$coC = $charac->ContestParticipations->count();

jQ('$("#character").tabs().find( ".ui-tabs-nav" ).sortable({ axis: "x" });');
echo tag('div', array('id' => 'character'), NULL), tag('ul', array('class' => 'showThis'),
  tag('li', tag('a', array('href' => '#general'), lang('general'))) .
  tag('li', tag('a', array('href' => '#stats'), lang('shop._stats'))) .
  ( count($items) ? tag('li', tag('a', array('href' => '#items'), pluralize(lang('shop._items'), count($items)))) : '' ) .
  ( $charac->getSpellCount() ? tag('li', tag('a', array('href' => '#spells'), pluralize(ucfirst(lang('character.spell')), $charac->getSpellCount()))) : '' ) .
  ( count($charac->getJobs()) ? tag('li', tag('a', array('href' => '#jobs'), pluralize(ucfirst(lang('character.job')), count($charac->getJobs())))) : '' ) .
  ( $evC || $coC ? tag('li', tag('a', array('href' => '#activity'), lang('activity'))) : '' ) .
  ( level(LEVEL_ADMIN) ? tag('li', tag('a', array('href' => '#give'), lang('character.give'))) : '' )
 ),
 tag('div', array('id' => 'general'));

echo tag('b', lang('pseudo') . ': '), $charac->name, ', ';

if ($acc == $account) //it's YOUR account
	printf(lang('character.on_acc'));
else
	printf(lang('character.on_acc_of'), make_link($acc));

echo '.', tag('br'), $charac->toString(false);

if ($charac->relatedExists('GuildMember'))
{ //show the guild info
	printf(lang('character.guild_infos'), lang('guild.rank.' . $charac->GuildMember->rank),
	 make_link($charac->GuildMember->Guild), $charac->GuildMember->Guild->lvl);
}
if (level(LEVEL_MJ) && $map = $charac->getMap()) /* @var $map Map */
	printf(lang('character.pos'), $charac->name, $charac->map, $map->getPosX(), $map->getPosY(), $charac->cell);

echo '</div>', tag('br', array('class' => 'hideThis')), tag('h3', array('class' => 'hideThis'), lang('shop._stats')),
 tag('div', array('id' => 'stats'), $charac->getStatsHTMLTable()), tag('br', array('class' => 'hideThis'));


if (count($items))
{
	echo tag('div', array('id' => 'items'));
	$itemCount = sprintf(lang('has'), $charac->name, count($items),
					pluralize(lang('character.item'), count($items)));

	$stuff = tag('div', array(
		'id' => 'stuff',
	 )) . make_img('stuff', EXT_PNG, array(
		'style' => 'position: absolute;',
	)); //opening of the tag ...
	$posOffset = ItemTable::getInstance()->getPosOffset();
	foreach ($itemsInfo as $itemInfo)
	{ //$itemInfo = [instanceof Item, stats]
		// @var $item Items 
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
	echo $itemCount, $stuff, str_repeat(tag('br'), 12), '</div></div>';
	IG::registerEffectsTooltip(); //hover tooltips with item effects
}
if (count($charac->getSpells()))
{ //I SEE NO REASON FOR THIS TO HAPPEN. That's why I created this if.
	$align = array(//HTML attributes
		'align' => 'center',
		'valign' => 'middle',
	);
	//spell list
	echo tag('div', array('id' => 'spells')), str_repeat(tag('br', array('class' => 'hideThis')), 2), tag('table', array('border' => '1', 'style' => 'width: 100%')), tag('thead'), tag('tr',
	   tag('td', $align + array('style' => 'width: 42%;'), tag('b', lang('character.spell_name'))) .
	   tag('td', $align + array('style' => 'width: 23%;'), tag('b', lang('character.spell_level'))) .
	   tag('td', $align + array('style' => 'width: 35%;'), tag('b', lang('character.spell_pos')))) .
	  '</thead>' . tag('tbody', array('style' => 'height: 550px; overflow: auto'));

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
}

if (count($charac->getJobs()))
{
	echo '
<div id="jobs">
	<table>';
	foreach ($charac->getJobs() as $jobs)
	{
		echo '<tr>';
		foreach ($jobs as $job)
		{
			echo tag('td', $job == '' ? '' : make_img('jobs/' . $job[0], EXT_PNG, array('title' => lang('level') . ': ' . $job[2])));
		}
		echo '</tr>';
	}

	echo '
	</table>
</div>';
}

if ($evC || $coC)
{
	echo tag('div', array('id' => 'activity')), tag('table', array('border' => 1, 'width' => '100%')), tag('thead');

	if ($evC)
		echo tag('th', pluralize(lang('event'), $evC));
	if ($coC)
		echo tag('th', pluralize(lang('contest'), $coC));

	echo '
		</thead>
		<tbody>
			<tr>';

	if ($evC)
	{
		echo '
				<td>';
//					<ul>';
		foreach ($charac->Events as $event)
		{
			if ($event->relatedExists('Guild'))
			{
				if (!level(LEVEL_LOGGED))
					continue;
				if (!$char = $account->getMainChar())
					continue;
				if (!$char->relatedExists('GuildMember'))
					continue;
				if ($char->GuildMember->guild != $event->Guild->id)
					continue;
			}

//			echo '<li>';
			if ($event->isFinished())
			{
				if ($event->relatedExists('Winner') && $event->Winner->guid == $charac->guid)
					printf(lang('event.won'), $event);
				else
					printf(lang('event.participated_to'), $event);
			}
			else
				printf(lang('event.participate_to'), $event);

			echo '.<br />';//</li>';
		}
		echo 
			//		</ul>
'				</td>';
	}
	if ($coC)
	{
		echo '
				<td>';
//					<ul>';
		foreach ($charac->ContestParticipations as $cp)
		{
			if ($cp->Contest->ended)
			{
				if ($cp->position == 0)
					printf(lang('contest.participated_to'), make_link($cp->Contest));
				else
					printf(lang('contest.participated_pos'), $cp->position, make_link($cp->Contest));
			}
			else
				printf(lang('contest.participate_to'), make_link($cp->Contest));
		}
		echo  
					//</ul>
'				</td>';
	}

	echo '
			</tr>
		</tbody>
	</table>
</div>';
}

if (level(LEVEL_ADMIN))
{
	echo tag('br', array('class' => 'hideThis')), tag('div', array('id' => 'give'), make_form(array(
		 array('type', lang('action') . '&nbsp;', 'select', $types),
		 array('value', lang('value') . '&nbsp;'),
		 array('id', null, 'hidden', '' . $charac->guid),
	 ), '@character.give'));
}

echo '</div>';
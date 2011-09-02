<?php
echo make_form(array(
	array('name', lang('name') . tag('br'), NULL, $event->name),
	array('period', tag('br') . lang('datetime') . tag('br'), 'datetime', $event->period, array('__restrict' => '@today+;')),
	( level(LEVEL_ADMIN) && $guildId != -1 ? array('is_guild', tag('br') . lang('event.is_guild') . tag('br'), 'checkbox', $event->relatedExists('Guild')) : NULL ),
	( level(LEVEL_ADMIN) ? array('reward', tag('br') . lang('reward') . tag('br'), 'record', array('type' => 'one', 'model' => 'ShopItem', 'empty' => true), $event->reward_id) : NULL ),
));


if (level(LEVEL_ADMIN))
{
	if ($event->relatedExists('Guild'))
		jQ('$("#form_couple_reward").toggle();');

	jQ('
$("#form_is_guild").change(function ()
{
	$("#form_couple_reward").toggle();
});');
}
<?php
echo make_form(array(
	array('name', lang('name') . tag('br'), NULL, $event->name),
	array('capacity', tag('br') . lang('capacity') . ' (' . tag('i', lang('event.capacity_explain')) . ')' . tag('br'), NULL, $event->capacity),
	array('period', tag('br') . lang('datetime') . tag('br'), 'datetime', $event->period, array('__restrict' => '@today+;')),
	( level(LEVEL_ADMIN) ? array('is_tombola', tag('br') . lang('event.tombola.explain') . tag('br'), 'checkbox', $event->is_tombola) : NULL ),
	( level(LEVEL_ADMIN) && $guildId != -1 ? array('is_guild', tag('br') . lang('event.is_guild') . tag('br'), 'checkbox', $event->relatedExists('Guild')) : NULL ),
	( level(LEVEL_ADMIN) ? array('reward', tag('br') . lang('reward') . tag('br'), 'record', array('type' => 'one', 'model' => 'Items', 'empty' => true, 'parent' => 'ShopCategory', 'pColumn' => 'name'), $event->reward_id) : NULL ),
));


if (level(LEVEL_ADMIN))
{
	jQ('
var couple_reward = $("#form_couple_reward"),
	couple_is_tombola = $("#form_couple_is_tombola");
$("#form_is_guild").change(function ()
{
	is_guild = !is_guild;
	couple_reward.toggle();
	couple_is_tombola.toggle();
});

$("#form_capacity").keyup(function ()
{
	var $this = $(this);
	if ($this.val() == 0)
		couple_reward.hide();
	else if (!is_guild)
		couple_reward.show();
});');

	jQ('var is_guild = false;');
	if ($event->relatedExists('Guild'))
		jQ('couple_reward.toggle(); couple_is_tombola.toggle(); is_guild = true;');
}
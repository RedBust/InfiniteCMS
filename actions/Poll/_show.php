<?php
$name = $poll->getName(); //here, poll name
$inList = $router->getAction() == 'index';
$base_eip_url = array(
	'controller' => $router->getController(),
	'action' => 'update',
	'output' => 0,
	'header' => 0,
	'id' => $poll->id
);

if ($inList)
	$name = make_link($poll);
else if (level(LEVEL_ADMIN))
	$name = tag('div', array('id' => 'poll_name'), $name);

if (level(LEVEL_ADMIN))
{
	jQ(sprintf('
$("#poll_name").editInPlace({url: %s});', javascript_val(to_url($base_eip_url + array('col' => 'name')))));
	global $calendar_opts;
	$types = array('start' => 'min', 'end' => 'max');
	$js = '';
	foreach ($types as $type => $type_name)
	{
		$other = $type == 'start' ? 'end' : 'start';
		jQ('var date' . $type . ' = $.datepicker.parseDate("yy-mm-dd", "' . $poll->{'date_'.$type} . '")');
		//hours spent hier : 
		// ... enough to make me cry :( (at least)
		$js .= strtr('//HERE I PROVE THAT STRTR IS FAR BEYOND THE SKY. btw, the whole thing proves that strtr() have been created by god. Wait, God created PHP ? That explains so much, and yet so little ...
		dp%type%%id% = $("#%type%DateDP%id%"),
		dp%type%%id%Content = $("#%type%Date%id%"),
		dp%type%%id%Edit = $("<img />", {src: %edit_img%});
	dp%type%%id%.datepicker(
	{
		%other_value%Date: date%other_type%,
		onSelect: function (dateText, inst)
		{
			dp%type%%id%Content.html(dateText).load(%update_url%, {"update_value": dateText, "_csrf_token": "%token%"}, function (response, status)
			{
				date = $.datepicker.parseDate("dd/mm/yy", response);
				dp%other_type%%id%.datepicker("option", "%type_name%Date", date);
				//dp%type%.datepicker("option", "%other_type%Date", date);
			});
			dp%type%%id%.hide();
		},
		%calendar_opts%,
		showButtonPanel: true,
	}).hide();
	dp%type%%id%.after(dp%type%%id%Edit.click(function ()
	{
		if (dp%type%%id%.is(":visible"))
			dp%type%%id%.hide();
		else
			dp%type%%id%.show();
	}));', array(
			'%type%' => $type,
			'%id%' => $poll->id,
			'%type_name%' => $type_name,
			'%edit_img%' => javascript_val(url_for_image('icons/calendar_edit', EXT_PNG)),
			'%update_url%' => javascript_val(to_url($base_eip_url + array('col' => 'date_'.$type))),
			'%other_type%' => $other,
			'%other_value%' => $types[$other],
			'%calendar_opts%' => $calendar_opts,
			'%token%' => session_id(),
		));
	}
	jQ($js);
}
$canVote = level(LEVEL_LOGGED) ? $account->User->canVote($poll) : false;
printf('
		<div class="post">
			<div class="content">
				<div class="infos">
					<div class="title" id="title%d">
						%s%s%s
					</div>
					<div class="autre">
						<b>%s:</b> <span id="startDate%1$d">%s</span><span id="startDateDP%1$d"></span>. <b>%s:</b> <span id="endDate%1$s">%s</span><span id="endDateDP%1$d"></span>.
					</div>
				</div>
				<br /><p align="center" class="cont">
					<ul>', $poll->id, level(LEVEL_ADMIN) ? $poll->getUpdateLink() : '', $name, $poll->isElapsed() ? ' (' . lang('poll.elapsed') . ')' : '',
	lang('poll.date_start'), date_to_picker($poll->date_start), lang('poll.date_end'), date_to_picker($poll->date_end));

foreach ($poll->Options as $option)
{ /* @var $option PollOption */
	$name = lang($option->name, 'common', '%%key%%'); //here, option name
	printf('
						<li>
							%s<b>%s</b>: %d%%<!-- escape %% with double--> (%d) %s
						</li>', level(LEVEL_ADMIN) ? $option->getDeleteLink() : '',
	 $name, $option->getPercent(), $option->Polleds->count(), $canVote ? '&bull;&nbsp;' . $option->getVoteLink() : '');
}

if (level(LEVEL_ADMIN))
	echo tag('li', $poll->getCreateOptionLink());
	
echo '
					</ul>
				</p>
			</div>
		</div>';
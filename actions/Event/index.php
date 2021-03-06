<?php
$minYear = date('Y')-5;
$maxYear = date('Y')+5;
$year = $router->requestVar('year', 0);
if (!is_numeric($year) || $year < $minYear || $year > $maxYear)
	$year = date('Y');
$year = intval($year);
$month = $router->requestVar('month', 0);
if (!is_numeric($month) || $month < 1 || $month > 12)
	$month = date('m');
$month = intval($month);

$table = EventTable::getInstance();
$guild_id = 0;
if (level(LEVEL_LOGGED) && $c = $account->getMainChar())
{
	if ($c->relatedExists('GuildMember') && $c->GuildMember->relatedExists('Guild'))
		$guild_id = $c->GuildMember->guild;
}
$dates = array(
	array($month == 1 ? $year - 1 : $year, $month == 1 ? 12 : $month - 1),
	array($year, $month),
	array($month == 12 ? $year + 1 : $year, $month == 12 ? 1 : $month + 1),
);
$allEvents = $table->findByYearMonthAndMGuildId($dates, $guild_id);
$prevEvents = $events = $nextEvents = array();

foreach ($allEvents as $event)
{
	$eventDate = array($event->getYear(), $event->getMonth());

	if ($dates[0] == $eventDate)
		$prevEvents[] = $event;
	else if ($dates[1] == $eventDate)
		$events[] = $event;
	else
		$nextEvents[] = $event;
}

$date = new Datetime($year . '-' . $month . '-1');
$prevMonthDays = cal_days_in_month(CAL_GREGORIAN, $dates[0][1], $dates[0][0]);
$monthDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

$days = $table->sort($events);

$firstDay = $date->format('N') - 1; //we don't compense the exact number, only [1st of the month]-1

if (level(LEVEL_ADMIN))
{
	echo make_link(new Event) . tag('br') . tag('br');
}

$months = array();
foreach (range(1, 12) as $i)
	$months[$i] = lang('month.full.' . $i, 'calendar');
echo make_form(array(
	array('month', NULL, 'select', $months, $month),
	array('year', NULL, 'select', array_combine(range($minYear, $maxYear), range($minYear, $maxYear)), $year),
), '#', array('sep_inputs' => ' ', 'method' => 'GET', 'submit_hideThis' => true));
jQ('
$("#form_month").change(function (val)
{
	if (val == ' . $month . ')
		return;
	this.form.submit();
});
$("#form_year").change(function (val)
{
	if (val == ' . $year . ')
		return;
	this.form.submit();
});');

echo '
<table border="1" style="width: 100%;">
	<tr style="width: 100%;">';
foreach (range(1, 7) as $day)
	echo tag('th', tag('b', lang('day.full.' . $day, 'calendar')));
echo '
	</tr>';
$prevMonthDaysTr = $prevMonthDays - $firstDay;
if ($prevMonthDaysTr > 0 && $firstDay > 0)
{ //$firstDay > 0 is not needed, because else, the <tr will be opened + having $next to -1 will avoid a new tr,
	//and the for() will not trigger since $firstDay would be 0 ...
	$prevDays = $table->sort($prevEvents);

	echo '<tr>';
	for ($i = 0; $i < $firstDay; ++$i)
		echo tag('td', array('style' => array('background-color' => 'grey')), $table->display($prevDays, ++$prevMonthDaysTr));
	$next = -1; //avoid new tr.
}
else
	$next = 0;
foreach (range($firstDay + 1, $firstDay + $monthDays) as $countDay)
{
	if ($next == 0)
		echo '<tr>';
	if ($next == -1)
		$next = 0; 

	$day = $countDay - $firstDay;
	
	echo tag('td', $table->display($days, $day));

	if ($day == $monthDays + 1)
		break;
	++$next;
	if (($day != 1 || $firstDay > 1) && ($countDay % 7) == 0)
	{
		echo '</tr>';
		$next = 0;
	}
}
if ($next != 0)
{
#	$nextMonthDays = cal_days_in_month(CAL_GREGORIAN, $dates[2][1], $dates[2][0]);
	$nextDays = $table->sort($nextEvents);


	for ($i = 0; $i < 7 - $next; ++$i)
		echo tag('td', array('style' => array('background-color' => 'grey')), $table->display($nextDays, $i + 1));
	echo '</tr>';
}

echo '
</table>';
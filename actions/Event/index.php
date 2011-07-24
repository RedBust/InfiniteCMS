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
$events = $table->findByYearAndMonth($year, $month);
$prevEvents = $table->findByYearAndMonth(($month == 1 ? $year - 1 : $year), ($month == 1 ? 12 : $month - 1));
$nextEvents = $table->findByYearAndMonth(($month == 12 ? $year + 1 : $year), ($month == 12 ? 1 : $month + 1));

$date = new Datetime($year . '-' . $month . '-1');
$prevMonthDays = cal_days_in_month(CAL_GREGORIAN, ($month == 1 ? 12 : $month - 1), ($month == 1 ? $year - 1 : $year));
$monthDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

$prevDays = $table->sort($prevEvents);
$days = $table->sort($events);

$firstDay = $date->format('N') - 1; //we don't compense the exact number, only [1st of the month]-1


$months = array();
foreach (range(1, 12) as $i)
	$months[$i] = lang('month.full.' . $i, 'calendar');
echo make_form(array(
	array('month', NULL, 'select', $months, $month),
	array('year', NULL, 'select', array_combine(range($minYear, $maxYear), range($minYear, $maxYear)), $year),
), APPEND_FORM_TAG, '#', array('sep_inputs' => ' '));
jQ('
$("#form_send").hide();
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
	</tr>
	<tr>';
$prevMonthDaysTr = $prevMonthDays - $firstDay;
for ($i = 0; $i < $firstDay; ++$i)
{
	echo tag('td', array('style' => array('background-color' => 'grey')), $table->display($prevDays, ++$prevMonthDaysTr));
}
$next = 0;
foreach (range($firstDay + 1, $firstDay + $monthDays) as $countDay)
{
	$day = $countDay - $firstDay;
	
	echo tag('td', $table->display($days, $day));

	if ($day == $monthDays + 1)
		break;
	++$next;
	if (($day != 1 || $firstDay > 1) && ($countDay % 7) == 0)
	{
		echo '</tr><tr>';
		$next = 0;
	}
}
if ($next != 0)
{
//	$nextMonthDays = cal_days_in_month(CAL_GREGORIAN, ($month == 12 ? 1 : $month + 1), ($month == 12 ? $year + 1 : $year));
	$nextDays = $table->sort($nextEvents);


	for ($i = 0; $i < 7 - $next; ++$i)
		echo tag('td', array('style' => array('background-color' => 'grey')), $table->display($nextDays, $i + 1));
}

echo '
	</tr>
</table>';
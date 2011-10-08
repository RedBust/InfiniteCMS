<?php
$page = $router->requestVar('id', 1);
$char = '';

$orderBy = $router->requestVar('orderBy');
$orders = array(
	'level' => 'xp',
	'kamas',
	'honor',
	'deshonor',
);
$ordersLang = array();
foreach ($orders as $k => $order)
{
	$key = is_string($k) ? $k : $order;
	$ordersLang[$order] = ucfirst(lang('acc.ladder.' . $key, NULL, $order));
}
if (isset($orders[$orderBy]))
	$orderBy = $orders[$orderBy]; //Such as level => xp
if (!in_array($orderBy, $orders))
	$orderBy = reset($orders);

if (!in_array($m = strtoupper($router->requestVar('orderMode')), array('ASC', 'DESC')))
	$m = 'DESC';

$breeds = IG::getBreeds();
$breeds[-1] = lang('empty');
$genders = IG::getGenders();
$genders[-1] = lang('empty');

$sBreed = isset($breeds[$breed = $router->requestVar('breed')]) ? $breed : -1; //selected breed
$sGender = isset($genders[$gender = $router->requestVar('gender')]) ? $gender : -1; //selected gender


$char = $router->postVar('character');
if (!empty($char))
{
	$prev = Query::create()
				->select('COUNT(c.guid) AS prev')
					->from('Character c')
				->where('c.guid <= (SELECT sc.guid FROM Character sc WHERE sc.name = ?)', $char)
				->orderBy(sprintf('c.%s %s', $orderBy, $m));
	if (!$config['LADDER_ADMIN'])
	{
		$prev->leftJoin('c.Account a')
				->andWhere('a.level = 0');
	}
	if ($sBreed != -1)
		$prev->andWhere('c.class = ?', $sBreed);
	if ($sGender != -1)
		$prev->andWhere('c.sexe = ?', $sGender);
	$prev = $prev->fetchOneArray();
	$prev = $prev['prev'];
	if (0 == $prev)
	{
		$char = '';
		$page = 1;
		echo tag('span', array('style' => array('color' => 'red')), lang('character.does_not_exists')) . tag('br');
	}
	else
	{
		$page = ($prev - ($prev % $config['LADDER_LIMIT'])) / $config['LADDER_LIMIT'];
		++$page;
	}
}

$ladderDql = Query::create()
				->from('Character c')
					->leftJoin('c.Account a')
						->leftJoin('c.GuildMember gm')
							->leftJoin('gm.Guild g')
					->where('a.banned = 0');
if (!$config['LADDER_ADMIN'])
	$ladderDql->andWhere('a.level = 0');
if ($sBreed != -1)
	$ladderDql->andWhere('c.class = ?', $sBreed);
if ($sGender != -1)
	$ladderDql->andWhere('c.sexe = ?', $sGender);
$ladderDql->orderBy(sprintf('c.%s %s', $orderBy, $m));
$pager = new Doctrine_Pager($ladderDql, $page, $config['LADDER_LIMIT']);
$persos = $pager->execute();
/* @var $persos Collection */
$layout = new Doctrine_Pager_Layout($pager, new Doctrine_Pager_Range_Sliding(array('chunk' => 4)), to_url(array('controller' => $router->getController(), 'action' => $router->getAction(), 'orderBy' => $orderBy, 'orderMode' => $m, 'gender' => $sGender, 'breed' => $sBreed, 'id' => ''), false));
$layout->setTemplate('[<a href="{%url}">{%page}</a>]');
$layout->setSelectedTemplate('[<b>{%page}</b>]');

echo tag('fieldset', tag('legend', array('id' => 'search'), lang('character.search') . tag('span', array('class' => 'showThis'), $sGender == -1 && $sBreed == -1 && empty($char) ? ' >' : ' <')) .
 tag('div', array('class' => $sGender == -1 && $sBreed == -1 && empty($char) && $orderBy == reset($orders) && $m == 'DESC' ? 'hideThis' : ''), make_form(array(
	array('character', lang('name'), NULL, $char),
	array('orderBy', lang('ladder.order_by'), 'select', $ordersLang, $orderBy),
	array('orderMode', lang('ladder.order_mode'), 'select',
	 array('DESC' => lang('ladder.order_mode.DESC'), 'ASC' => lang('ladder.order_mode.ASC')), $m),
	array('gender', lang('acc.ladder.sex'), 'select', $genders, $sGender),
	array('breed', lang('acc.ladder.class'), 'select', $breeds, $sBreed),
)))), str_repeat(tag('br'), 3);
jQ('var searchForm = $("fieldset").find("div"),
	searchVisible = ' . ( $gender == -1 && $breed == -1 && empty($char) && $orderBy == reset($orders) && $m == 'DESC' ? 'false' : 'true' ) . ';
	searchLegend = $("#search").click(function ()
{
	searchForm.slideToggle();
	searchVisible = !searchVisible; //DON\'T ASK ME WHY searchForm.is(":visible") DOESN\'T WORK, I AIN\'T GOT A CLUE ! SHITTY LIB
	if (searchVisible) //and just calling searchForm.is(":visible") in the browser\'s console works fine ... FUCK YOU.
		searchLegend.find("span").html(" <");
	else
		searchLegend.find("span").html(" >");
});');
$persos->ladderDisplay(($layout->getPager()->getPage() - 1) * $config['LADDER_LIMIT'], $char);
echo paginate($layout);

if (!$persos->count())
	echo tag('b', lang('acc.ladder.no_character'));
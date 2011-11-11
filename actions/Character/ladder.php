<?php
$page = $router->requestVar('id', 1);
$char = '';
$table = CharacterTable::getInstance();
$contest = ContestTable::getInstance()->retrieve();
if (!$contest)
	$contest = NULL;

$orderBy = $router->requestVar('orderBy');
$orders = array(
	'kamas',
	'honor',
	'deshonor',
	'level' => 'xp',
);
if ($contest)
	$orders[] = 'votes';
$ordersLang = array();
foreach ($orders as $k => $order)
{
	$key = is_string($k) ? $k : $order;
	$ordersLang[$order] = ucfirst(lang('acc.ladder.' . $key, NULL, $order));
}
if (isset($orders[$orderBy]))
	$orderBy = $orders[$orderBy]; //Such as level => xp
if (!in_array($orderBy, $orders))
	$orderBy = end($orders);

if (!in_array($m = strtoupper($router->requestVar('orderMode')), array('ASC', 'DESC')))
	$m = 'DESC';

$breeds = IG::getBreeds();
$breeds[-1] = lang('empty');
$genders = IG::getGenders();
$genders[-1] = lang('empty');

$sBreed = isset($breeds[$breed = $router->requestVar('breed')]) ? $breed : -1; //selected breed
$sGender = isset($genders[$gender = $router->requestVar('gender')]) ? $gender : -1; //selected gender


$char = urldecode($router->requestVar('character'));
if (!empty($char))
{
	$prev = Query::create()
				->select('COUNT(c.guid) AS prev');
	if ($contest)
	{
		$prev
				->from('ContestParticipant cp')
					->leftJoin('cp.Character c')
				->andWhere('cp.contest_id = ?', $contest->id);
	}
	else
	{
		$prev->from('Character c');
	}
	$prev
				->andWhere('c.guid <= (SELECT sc.guid FROM Character sc WHERE sc.name = ?)', $char)
				->orderBy(sprintf('%s.%s %s', $orderBy == 'votes' ? 'cp' : 'c', $orderBy, $m));
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


$ladderDql = Query::create();
if ($contest)
{ //@todo partial ?
	echo tag('h1', lang('contest') . ' : ' . js_link('contestInfo.dialog("open")', $contest->getName())),
	 tag('div', array('id' => 'contestInfo', 'title' => $contest->getName()));
	partial('ContestJuror/index', array('contest'), PARTIAL_CONTROLLER);
	if ($contest->relatedExists('Reward'))
	{
		echo tag('br') . tag('fieldset', array('id' => 'reward'),
		 tag('legend', tag('b', lang('reward'))) .
		 tag('div', $contest->Reward)) . tag('br');
	}
	echo '</div>';

	if (level(LEVEL_LOGGED) && $account->canCompete($contest))
		echo $contest->getParticipateLink(), tag('br'), tag('br'); //@todo allow unregister ?

	jQ('
var contestInfo = $("#contestInfo").dialog(dialogOpt);
pageBind(function ()
{
	contestInfo.dialog("close");
	delete contestInfo;
});');

	$ladderDql
				->from('ContestParticipant cp')
					->leftJoin('cp.Character c')
				->andWhere('cp.contest_id = ?', $contest->id);
}
else
	$ladderDql->from('Character c');
$ladderDql
					->leftJoin('c.Account a')
						->leftJoin('c.GuildMember gm')
							->leftJoin('gm.Guild g')
					->andWhere('a.banned = 0');
if (!$config['LADDER_ADMIN'])
	$ladderDql->andWhere('a.level = 0');
if ($sBreed != -1)
	$ladderDql->andWhere('c.class = ?', $sBreed);
if ($sGender != -1)
	$ladderDql->andWhere('c.sexe = ?', $sGender);
$ladderDql->orderBy(sprintf('%s.%s %s', $orderBy == 'votes' ? 'cp' : 'c', $orderBy, $m));

$urlMask = to_url(array('controller' => $router->getController(), 'action' => $router->getAction(), 'orderBy' => $orderBy, 'orderMode' => $m, 'gender' => $sGender, 'breed' => $sBreed, 'contest' => $contest ? $contest->id : -1, 'id' => ''), false);
$chunk = 4;
if ($contest)
{ //omg ... gonna move that ...
	$totalPersos = $ladderDql->execute();
	$persos = new Collection('Character');
	$i = 0;
	$lastVotes = NULL;
	$rangePerso = range($start = 1 + ($page - 1) * $config['LADDER_LIMIT'], $end = $start - 1 + $config['LADDER_LIMIT']);

	foreach ($totalPersos as $record)
	{
		if ($record->votes != $lastVotes)
		{
			++$i;
			$lastVotes = $record->votes;
		}

		if (in_array($i, $rangePerso))
		{
			$persos->add($record);
		}

		if ($i > $end)
			break;
	}
	$lastPage = ceil($i / $config['LADDER_LIMIT']);
	$rangePage = range(1, $lastPage);
	$showRange = range($page - $chunk / 2, $page + $chunk / 2);
	$dispPages = array_intersect($showRange, $rangePage);
	--$start;
	//if you can read minds, you can read mine here : FUCK YOU MYSQL NOT ALLOWING LIMIT IN SUBQUERIES ! FUUUUU-
}
else
{
	$pager = new Doctrine_Pager($ladderDql, $page, $config['LADDER_LIMIT']);
	$persos = $pager->execute();
	/* @var $persos Collection */
	$layout = new Doctrine_Pager_Layout($pager, new Doctrine_Pager_Range_Sliding(array('chunk' => $chunk)), $urlMask);
	$start = ($layout->getPager()->getPage() - 1) * $config['LADDER_LIMIT'];
	$layout->setTemplate('[<a href="{%url}">{%page}</a>]');
	$layout->setSelectedTemplate('[<b>{%page}</b>]');
}

echo $table->getSearch();
$persos->ladderDisplay($start, $contest, $char);
if ($contest)
	echo paginate($page, $lastPage, $dispPages, $urlMask);
else
	echo paginateLayout($layout);

if (!$persos->count())
	echo tag('b', lang('acc.ladder.no_character'));

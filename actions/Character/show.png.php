<?php
$table = CharacterTable::getInstance();
if (!( $charac = $table->retrieve() ))
{
	echo lang('character.does_not_exists');
	return;
}
/* @var $charac Character */

if (!function_exists('gd_info'))
	exit('gd not installde');


define('HEADERS_SENT', true);
header('Content-type: image/png');

Cache::ensureDir('profileCards');
Cache::ensureDir('profileCards/Character');
$filename = Cache::getDir() . 'profileCards/Character/' . $charac->name . '.' . EXT_PNG;
if (file_exists($filename) && !date_passed(filemtime($filename), '+6 hours'))
{
	readfile($filename);
	ob_end_flush();
	exit;
}

$image = @imagecreatetruecolor(600, 130);
if (!$image)
	exit('error creating the image. Please check the gd installation.');
$text_color = imagecolorallocate($image, 255, 255, 255);



imagestring($image, 20, 3, 5, $charac->name, $text_color);
if ($charac->relatedExists('Account'))
	imagestring($image, 3, 5, 18, sprintf(lang('character._on_acc_of'), $charac->Account->pseudo) . '.', $text_color);

$main_infos = IG::getBreed($charac->class) . ' ' . IG::getGender($charac->sexe) .
	' (lv ' . $charac->level  . ').';
imagestring($image, 3, 5, 34, $main_infos, $text_color);

if ($charac->relatedExists('GuildMember') && $charac->GuildMember->relatedExists('Guild'))
{
	if ($charac->GuildMember->rank == 1)
		$guild_title = 'character.leader_of';
	else
		$guild_title = 'character.member_of';

	$guild_infos = sprintf(lang($guild_title), $charac->GuildMember->Guild->name) .
		' (lv' . $charac->GuildMember->Guild->lvl . ').';
	imagestring($image, 3, 5, 46, $guild_infos, $text_color);
}

if ($charac->alignement != 0)
{
	$align_infos = lang('align.' . $charac->alignement) .
	 ' (' . strtolower(lang('align.lvl.' . $charac->alvl)) . '), ' .
	 $charac->honor . 'h' . (empty($charac->deshonor) ? '' : '/' . $charac->deshonor . 'dh') . '.';
	imagestring($image, 3, 5, 62, $align_infos, $text_color);
}

imagestring($image, 3, 5, 78, number_format($charac->kamas, 0, '.', ' ') . 'K / ' .
 $charac->capital . ' ' . lang('statspoints') . ' / ' .
 $charac->spellboost . ' ' . lang('spellspoints'), $text_color);

imagestring($image, 16, 320, $decalage = 5, lang('shop._stats_'), $text_color);
$decalage += 6; //margin : 16 between each block.
$stats = array(
	array('vitality', 'vitalite', '7d'),
	array('wisdom', 'sagesse', '7c'),
	array('strength', 'force', '76'),
	array('agility', 'agilite', '77'),
	array('chance', NULL, '7b'),
	array('intell', 'intelligence', '7e'),
);
$pos = range(0, 15);
foreach ($charac->getItems() as $item)
{ /* @var $item Item */
	if (in_array($item->pos, $pos))
		IG::parseStat($item->stats, false);
}
foreach ($stats as $stat)
{
	$decalage += 10;
	$name = $stat[0]; //english name
	$value = $charac[$stat[1] === NULL ? $name : $stat[1]];
	$stat_text = $value;
	$add = IG::getStat($stat[2]);
	if ($add != 0)
		$stat_text .= ' (' . ($add < 0 ? '-' : '+') . $add . ')';
	$stat_text .= ' ' . lang('shop.stat_.' . $name);
	imagestring($image, 3, 330, $decalage, $stat_text, $text_color);
}
$decalage += 3;
$stats = array(
	'6f', //AP
	'80', //PM
	'73', //CC
	'70', //dmg
	'8a', //%dmg
);
$statsAdd = array(
	'6f' => 6, //6AP on start
	'80' => 3, //3MP on start
);
foreach ($stats as $stat)
{
	$value = IG::getStat($stat);
	if (isset($statsAdd[$stat]))
		$value += $statsAdd[$stat];
	if ($value == 0)
		continue;

	$decalage += 10;
	imagestring($image, 3, 330, $decalage, $value . ' ' . lang($stat, 'stat'), $text_color);
}


imagestring($image, 3, 1, 115, '(c) ' . $config['SERVER_NAME'], imagecolorallocate($image, 50, 50, 50));

imagepng($image, $filename);
readfile($filename);
imagedestroy($image);
global $headers;
$headers = false;
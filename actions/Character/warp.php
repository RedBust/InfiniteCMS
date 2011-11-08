<?php
//this page is not meant to be used but you can link it anyway via : (by example, in templates/_shared/php/right.php)
//cette page est là à titre d'exemple, vous pouvez néanmoins faire un lien vers celle-ci avec : (par exemple, dans templates/_shared/php/right.php)
//<li>< ?php echo make_link(array('controller' => 'Character', 'action' => 'warp'), lang('Character - warp', 'title')) ? ></li>
//(no spaces here / pas d'espaces ici : < ? and ? >)

if (!check_level(LEVEL_LOGGED))
	return;
if (!$mainChar = $account->getMainChar())
	return; //no char to warp, this page shouldn't be accessible if that happens

$mainChar->map = 11503; //... watevar. Change it!
$mainChar->cell = 200; //re-... watevar. This should work.
//FTR : auto-save when __shutdown
printf(lang('character.main.warpd'), $mainChar->name);
//7 lines. so hard ... TRY TO DO IT URSELF BRO !!
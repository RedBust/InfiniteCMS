<?php
$txt = str_replace("\n", '', lang('acc.join'));
$txt = preg_replace('`{register}(.*){/register}`', $config['ENABLE_REG'] ? '$1' : '', $txt);
$txt = preg_replace('`{log}(.*){/log}`', level(LEVEL_LOGGED) ? '$1' : '', $txt);
echo tag('div', array('class' => 'post'), tag('div', array('class' => 'content'), strtr($txt, array(
	'%client%' => $config['DOWNLOAD']['CLIENT'],
	'%launcher.32%' => $config['DOWNLOAD']['LAUNCHER.32'],
	'%launcher.64%' => $config['DOWNLOAD']['LAUNCHER.64'],
	'%config%' => $config['DOWNLOAD']['CONFIG'],
	'%register%' => make_link(new Account, lang('here')))
)));
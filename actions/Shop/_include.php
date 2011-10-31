<?php
if (!level(LEVEL_LOGGED))
{
	echo lang('shop.must_be_logged');
	define('LEVEL_FALLBACK', true);
}

if (!$config['ENABLE_SHOP'] && !level(LEVEL_ADMIN))
{
	echo lang('shop.off');
	define('HTTP_CODE', 301); //W8 WAT ONLY ADMINS ARE AUTHORIZED ? "OP IS A FAGGOT"
}
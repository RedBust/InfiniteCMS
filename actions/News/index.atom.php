<?php
$lasts = intval($router->requestVar('lasts', -1));
if (!is_numeric($lasts) || $lasts < 1 || $config['ARTICLES_BY_PAGE'] * $config['ARTICLES_BY_PAGE'] > $lasts)
	$lasts = $config['ARTICLES_BY_PAGE'];

if (!$cache = Cache::start('news_index.atom'))
{
	echo Query::create()
			->from('News n')
				->limit($lasts)
			->execute()
			->atomDisplay();
}
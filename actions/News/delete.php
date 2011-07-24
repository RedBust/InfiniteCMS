<?php
if (!check_level(LEVEL_ADMIN))
	return;

$news = Query::create()
				->from('News')
				->where('id = ?', $id = $router->requestVar('id', -1))
				->fetchOne();
$back_to_index = ' ' . make_link('@root', lang('back_to_index'));

if (!$news)
{
	printf(lang('news.not_exists') . '%s', html($router->requestVar('id')), $back_to_index);
	return;
}
$deleteQ = Query::create()
				->delete()
				->from('Comment')
					->where('news_id = ?', $news->id);
$deleteQ->execute();
$deleteQ->free();
$news->delete();
Cache::deletePrefix($router->getController());
echo lang('news.deleted') . $back_to_index;

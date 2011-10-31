<?php
//@todo merge with update (?)
if (!check_level(LEVEL_LOGGED))
	return;

$router->codeUnless(404, $news = NewsTable::getInstance()->find($id = $router->requestVar('id', -1)));

/* @var $news News */
$back_msg = tag('br') . make_link($news, lang('back_to_news'));
if (empty($config['MAX_COMMENTS']))
{
	echo lang('news.com.disabled') . $back_msg;
	return;
}

if (!( $comment = $router->requestVar('comment') ))
{
	echo lang('news.com.need_text') . $back_msg;
	return;
}

/* @todo
if (!$account->User->canComment($news))
{
	echo lang('news.com.already_comment') . $back_msg;
	return;
}
*/

$com = new Comment;
$com->News = $news;
$com->Author = $account->User;
$com->content = level(LEVEL_ADMIN) ? $comment : html($comment);
$title = $router->requestVar('title');
if (strtolower($title) == strtolower(strip_tags($news->title))) //the case is not really significative
	$com->title = $title; //no html
else
	$com->title = html($title);
$com->save();
redirect($news);
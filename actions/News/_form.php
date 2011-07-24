<?php
$exists = $news instanceof News ? $news->exists() : true;
if (!$exists)
	$title = lang('News - new', 'title');
defined('FROM_INCLUDE') || define('FROM_INCLUDE', false);
$loc = FROM_INCLUDE ? to_url(array(
			'action' => 'update',
			'id' => $news['id'],
		)) : APPEND_FORM_TAG;
$code = ( FROM_INCLUDE ? '' : tag('br') . tag('h1', lang($router->getController() . ' - ' . ( $exists ? 'update' : 'new' ), 'title')) ) .
		make_form(array(
			array('title', lang('title') . tag('br'), NULL, $news['title']),
			array('content', lang('content') . tag('br'), 'textarea', $news['content']),
		), $loc);

if (FROM_INCLUDE)
	return $code;
else
{
	echo $code;
	jQ('
$("#form_content")
	.attr("id", "content")
	.attr("cols", 20)
	.attr("rows", 5);');
}
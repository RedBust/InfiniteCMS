<?php
if (!check_level(LEVEL_MODO))
	return;

if (!$com = CommentTable::getInstance()->find($id = $router->requestVar('id', -1)))
{
	define('HTTP_CODE', 404);
	return;
}

/* @var $com Commentaire */
//smart way (bad)
#		$com->content = lang( 'news.com.censored' ) . ' ' . sprintf( lang( 'by' ), $account->pseudo );
#		$com->save();
$com->delete();
redirect(array(
	'controller' => $router->getController(),
	'action' => 'show',
	'id' => $com->News->id,
));
<?php
if (!check_level(LEVEL_MODO))
	return;

$router->codeUnless(404, $com = CommentTable::getInstance()->find($id = $router->requestVar('id', -1)));

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
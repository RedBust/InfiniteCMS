<?php
if (!check_level(LEVEL_LOGGED))
	return;

$mode = $router->requestVar('mode', 'add');
$com = CommentTable::getInstance()->find($id = $router->requestVar('id', -1));
if (!$com && $mode !== 'add')
{
	echo lang('news.com.does_not_exists');
	return;
}
Cache::destroyPrefix($router->getController() . '_show');
switch ($mode)
{
	case 'censor':
		if (!check_level(LEVEL_MODO))
			return;
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
	break;
	case 'update':
	//edit in place (not working ATM, yeah ... "screw the EIP I've work")
		if (!check_level(LEVEL_MODO))
			return;

		$comTable = $com->getTable();
		/* @var $comTable CommentaireTable */
		$headers && print lang('updating_cols');
		$col = $router->requestVar('col', array());
		if (!$comTable->hasColumn($col) || empty($_POST['update_value']))
			$col = $comTable->getColumnNames();
		else
			$vals = array($col => $_POST['update_value']);
		foreach ((array) $col as $c)
		{
			if (empty($vals[$c]))
				$erreurs[] = sprintf(lang('must_!empty'), $c);
			else
				$com->$c = $vals[$c];
		}
		$com->save();
		if (!empty($col) && !is_array($col) && !$output) //AJaX
			exit($com->$col);

		redirect(array(
			'controller' => $router->getController(),
			'action' => 'show',
			'id' => $com->News->id,
		));
	break;
	#default:
	case 'add':
		if (!check_level(LEVEL_LOGGED))
			return;

		if (!( $news = NewsTable::getInstance()->find($id = $router->requestVar('id', -1)) ))
		{
			printf(lang('news.not_exists'), html($id)) . make_link('@root', lang('back_to_index'));
			return;
		}
		/* @var $news News */
		$news_url_ary = array('controller' => $router->getController(), 'action' => 'show', 'id' => $id);
		$back_msg = tag('br') . make_link($news_url_ary, lang('back_to_news'));
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
		if (empty($title) || strtolower($title) == strtolower(strip_tags($news->title))) //the case is not really significative
			$com->title = $title; //no html
		else
			$com->title = html($title);
		$com->save();
		redirect($news_url_ary);
	break;
}
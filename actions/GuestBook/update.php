<?php
if (!check_level(LEVEL_LOGGED) || $config['RATES_BY_PAGE'] == -1)
	return;

/* @var $account Account */
if (!$account->User->canReview())
	return;

$review = new Review;
if (count($_POST) > 0)
{
	$review->Author = $account->User; //set up the author
	if (empty($_POST['comment'])) //no comment :( ?
	{
		$errors['comment'] = sprintf(lang('must_!empty'), 'comment');
	}
	else //process it
	{
		if (level(LEVEL_ADMIN))
			$comment = News::format($_POST['comment']);
		else //hash unless admin
			$comment = html($_POST['comment']);
		$review->comment = $comment;
	}
}
if (count($_POST) < 1 || $errors != array())
{
	partial('_form', array('review'), PARTIAL_CONTROLLER);
}
elseif (count($_POST) > 0 && $errors == array() && $headers)
{
	$review->save();
	redirect(array('controller' => $router->getController(), 'action' => 'index'));
}
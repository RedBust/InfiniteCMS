<?php
if (!empty($_POST))
{
	$receivers = $router->postVar('receivers');
	if (empty($receivers))
	{
		$errors[] = sprintf(lang('must_!empty'), 'receivers');
	}
	else
	{
		$receivers = explode(',', $receivers);
		$receiversAv = AccountTable::getInstance() //av = available
								->findReverseFriends()
								->fetchArray();
		$receiversIds = array();
		foreach ($receiversAv as $receiverAv)
		{
			if ($receiverAv['guid'] != $account->guid
			 && in_array($receiverAv['guid'], $receivers))
				$receiversIds[] = $receiverAv['guid'];
		}
		$receiversIds[] = $account->guid;
	}
	$title = $router->postVar('title');
	if (empty($title))
	{
		$errors[] = sprintf(lang('must_!empty'), 'title');
	}
	$msg = $router->postVar('message');
	if (empty($msg))
	{
		$errors[] = sprintf(lang('must_!empty'), 'content');
	}

	if (empty($errors))
	{
		$thread = new PrivateMessageThread();
		$thread->title = $title;

		foreach ($receiversIds as $id)
		{
			if ($id == $account->guid)
				continue;

			$receiver = new PrivateMessageThreadReceiver;
			$receiver->next_page = 1;
			$receiver->User = UserTable::getInstance()->fromGuid($id);
			$thread->Receivers[] = $receiver;
		}
		$receiver = new PrivateMessageThreadReceiver;
		$receiver->next_page = 1;
		$receiver->User = $account->User;
		$receiver->save();

		$answer = new PrivateMessageAnswer();
		$answer->Author = $account->User;
		$answer->message = $msg;

		$thread->Answers[] = $answer;
		$thread->save();
	}
}
if (count($_POST) < 1 || $errors != array())
{
	echo make_form(array(
		array('receivers', lang('pm.receivers') . tag('br')),
		array('title', lang('title') . tag('br')),
		array('message', lang('rate.msg') . tag('br'), 'textarea'),
	));
	jQ('$("#form_receivers").tokenInput("' . getPath() . 'Account/reverseFriends.json", {theme: "facebook", preventDuplicates: true});');
}
elseif (count($_POST) && $errors == array())
{
	redirect(array('controller' => $router->getController(), 'action' => 'show', 'id' => $thread->id));
}
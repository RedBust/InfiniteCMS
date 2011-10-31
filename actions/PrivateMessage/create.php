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
		$receiversAv = $account->getReverseFriends(); //av = available
		$receiversIds = array();
		foreach ($receiversAv as $receiverAv)
		{
			if ($receiverAv['guid'] != $account->guid
			 && in_array($receiverAv['guid'], $receivers))
				$receiversIds[] = $receiverAv['guid'];
		}
		$receiversIds[] = $account->guid;

		if ($receiversIds == array($account->guid))
			$errors[] = sprintf(lang('must_!empty'), 'receivers');
	}

	$title = $router->postVar('title');
	if (empty($title))
		$errors[] = sprintf(lang('must_!empty'), 'title');

	$msg = $router->postVar('message');
	if (empty($msg))
		$errors[] = sprintf(lang('must_!empty'), 'content');


	if (empty($errors))
	{
		$thread = new PrivateMessageThread();
		$thread->title = $title;

		$reveiversId = array_unique($receiversIds);
		foreach ($receiversIds as $id)
		{
			$receiver = new PrivateMessageThreadReceiver;
			$receiver->next_page = $id == $account->guid ? 0 : 1;
			$receiver->User = UserTable::getInstance()->fromGuid($id);
			$thread->Receivers[] = $receiver;
		}

		$answer = new PrivateMessageAnswer();
		$answer->Author = $account->User;
		$answer->message = $msg;

		$thread->Answers[] = $answer;
		$thread->save();
	}
}

if (empty($_POST) || !empty($errors))
{
	echo make_form(array(
		array('receivers', lang('pm.receivers') . tag('br')),
		array('title', lang('title') . tag('br')),
		array('message', lang('rate.msg') . tag('br'), 'textarea'),
	));
	jQ('$(function () { $("#form_receivers").tokenInput("' . getPath() . 'Account/reverseFriends.json", {theme: "facebook", preventDuplicates: true}); });');
}
else if (!empty($_POST) && empty($errors))
	redirect(array('controller' => $router->getController(), 'action' => 'show', 'id' => $thread->id));
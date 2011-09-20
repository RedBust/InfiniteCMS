<?php
$thread = Query::create()
				->from('PrivateMessageThread pmt')
					->leftJoin('pmt.Receivers pmr INDEXBY pmr.user_guid')
						->leftJoin('pmr.Account pmra')
					->andWhere('pmt.id = ?', $id = intval($router->requestVar('id')))
				->fetchOne();
if ($thread ? (!$thread->Receivers->contains($account->guid) && !level(LEVEL_ADMIN)) : true)
{
	echo lang('pm.does_not_exist');
	return;
}

$msg = $router->postVar('message');
if (!empty($msg))
{
	$answer = new PrivateMessageAnswer;
	$answer->Thread = $thread;
	$answer->message = $msg;
	$answer->Author = $account->User;
	$answer->save();
	$page = $answer->getPage();
	

	foreach ($thread->Receivers as $receiver)
	{
		if ($receiver->user_guid == $account->guid || $receiver->next_page != 0)
			continue;

		$receiver->next_page = $page;
		$receiver->save();
	}
	$thread->Receivers->save();

	redirect(array('controller' => $router->getController(), 'action' => 'show', 'id' => $thread->id, 'page' => $page));
}
echo sprintf(lang('must_!empty'), strtolower(lang('message')));
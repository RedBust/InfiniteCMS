<?php
$thread = Query::create()
				->from('PrivateMessageThread pmt')
					->leftJoin('pmt.Receivers pmr INDEXBY pmr.user_guid')
						->leftJoin('pmr.Account pmra')
					->andWhere('pmt.id = ?', intval($id = $router->requestVar('id')))
				->fetchOne();
if (!$thread || ($thread && !$thread->Receivers->contains($account->guid) && !level(LEVEL_ADMIN)))
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
	$prev = Query::create()
				->select('COUNT(pma.id) AS prev')
				->from('PrivateMessageAnswer pma')
				->where('pma.id < ?', $answer->id)
					->andWhere('pma.thread_id = ?', $thread->id)
				->fetchOneArray();
	$prev = $prev['prev'];
	$page = 1 + ($prev - ($prev % $config['PMA_BY_PAGE'])) / $config['PMA_BY_PAGE'];
	

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
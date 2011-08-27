<?php
$unreads = $account->User->getUnreadPM();

$threads = Query::create() //it'll be heavy (wrote at the very start)
				->from('PrivateMessageThread pmt')
				->leftJoin('pmt.Receivers pmr INDEXBY pmr.user_guid')
						->leftJoin('pmr.Account pmra') //Private Message Receiver's Account
					->leftJoin('pmt.Answers pma')
						->leftJoin('pma.Author pmau') //Private Message Answer's User
							->leftJoin('pmau.Account pmaua') //Private Message Answer's User's Account
				->orderBy('pma.created_at DESC')
				->where('pmt.id IN (SELECT pmtr.thread_id FROM PrivateMessageThreadReceiver pmtr ON pmtr.author_id = ?)', $account->guid)
				->execute();

echo make_link('@pm.create', lang('PrivateMessage - create', 'title'));

if (empty($threads))
{
	echo lang('pm.any');
}
else
{
	echo '
<ul>';
	$url = array('controller' => $router->getController(), 'action' => 'show');
	foreach ($threads as $thread)
	{
		$receivers = array();
		foreach ($thread->Receivers as $i => $receiver)
		{
			if ($receiver->user_guid != $account->guid)
				$receivers[] = make_link($receiver->Account);
		}
		$fAnswer = $thread->Answers->getFirst(); //fAnswer
		$new = $unreads->contains($thread->id) ? tag('b', tag('u', '!')) . '&nbsp;' : '';
		$_url = $url + array('id' => $thread->id);
		if ($thread->Receivers[$account->guid]->next_page != 0)
			$_url += array('page' => $thread->Receivers[$account->guid]->next_page);

		echo tag('li', sprintf(lang('pm.info'), $new . make_link($_url, html($thread->title)), '<i>' . implode('</i>, <i>', $receivers) . '</i>') .
		 sprintf(lang('pm.last_answer_by_on'), make_link($fAnswer->Author->Account), $fAnswer->created_at));
	}
echo '</ul>';
}
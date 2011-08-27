<?php
$page = $router->requestVar('page');
if (!is_numeric($page) || $page < 1)
	$page = 1;
$thread = Query::create()
				->from('PrivateMessageThread pmt')
					->leftJoin('pmt.Receivers pmr INDEXBY pmr.user_guid')
						->leftJoin('pmr.Account pmra')
					->andWhere('pmt.id = ' . intval($id = $router->requestVar('id')))
				->fetchOne();
if (!$thread || ($thread && !$thread->Receivers->contains($account->guid) && !level(LEVEL_ADMIN)))
{
	echo lang('pm.does_not_exist');
	return;
}

$answersDql = Query::create()
				->select('pma.id, pma.message, pma.thread_id, pma.created_at, pmau.guid, pmaua.*')
				->from('PrivateMessageAnswer pma')
					->leftJoin('pma.Author pmau') //Private Message Answer's User
						->leftJoin('pmau.Account pmaua') //Private Message Answer's User's Account
				->where('pma.thread_id = ?', $thread['id'])
				->groupBy('pma.id');
$pager = new Doctrine_Pager($answersDql, $page, $config['PMA_BY_PAGE']);
$answers = $pager->execute();
$layout = new Doctrine_Pager_Layout($pager, new Doctrine_Pager_Range_Sliding(array('chunk' => 4)), to_url(array('controller' => $router->getController(), 'action' => $router->getAction(), 'id' => $id, 'page' => '')));
$layout->setTemplate('[<a href="{%url}" class="link">{%page}</a>]');
$layout->setSelectedTemplate('[<b>{%page}</b>]');

echo make_link('@pm', lang('pm.back')), tag('br'), tag('br'),
 tag('h4', html($thread['title'])), '
<table style="width: 100%;" border="1">' .
 tag('tr', tag('th', lang('infos')) . tag('th', lang('rate.msg')));
foreach ($answers as $answer)
{
	$created = explode(' ', $answer->created_at);
	$msg = utf8_encode($answer['message']);
	if ($answer->Author->Account->level < LEVEL_ADMIN)
		$msg = html($msg);
	else
		$msg = News::format($msg);
	echo tag('tr', array('style' => array('width' => '20%;')),
	 tag('td', tag('b', make_link($answer->Author->Account)) . tag('br') . sprintf(lang('_the_at'), $created[0], $created[1])) .
	 tag('td', array('style' => array('width' => '80%')), $msg));
}
$rcv = $thread->Receivers[$account->guid];
if ($rcv->next_page != 0 && $rcv->next_page < $pager->getPage() + 1)
{ //update page IF !already read && !
	$rcv->next_page = $pager->getPage() == $pager->getLastPage() ? 0 : $pager->getPage() + 1;
	$rcv->save();
}
echo '</table>', paginate($layout);

echo tag('div', array('id' => 'pm-answer'), tag('br') .
 tag('h4', tag('b', lang('pm.answer'))) .
 make_form(array(
	array('message', lang('rate.msg') . tag('br'), 'textarea')
 ), array('controller' => $router->getController(), 'action' => 'answer', 'id' => $thread->id)));
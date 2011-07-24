<?php
if (!check_level(LEVEL_ADMIN))
	return;

if (NULL !== ( $title = $router->requestVar('title') ) && NULL !== ( $body = $router->requestVar('body') ))
{
	$accountsQ = Query::create()
							->select('email')
								->from('Account')
								#->where('level > ?', $gmRequired)
								;
	$accounts = $accountsQ->fetchArray();
	$accountsQ->free();

	$recipients = array();
	foreach ($accounts as $account)
	{
		$recipients[] = $account['email'];
	}
	mail(implode(', ', $recipients), $title, $content);
}
else
{
	echo tag('h1', lang()), make_form(array(
		array('title', NULL, lang('mail.title')),
		array('body', 'textarea', lang('content')),
	));
}

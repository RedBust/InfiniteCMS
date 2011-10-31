<?php
if (!check_level(LEVEL_ADMIN))
	return;

if (NULL !== ( $subject = $router->requestVar('subject') ) && NULL !== ( $body = $router->requestVar('body') ))
{
	$accounts = Query::create()
							->select('email')
								->from('Account')
								#->where('level > ?', $gmRequired)
								->fetchArray();

	$recipients = array();
	foreach ($accounts as $account)
		$recipients[] = $account['email'];
	mail(implode(', ', $recipients), $subject, $content);
}
else
{
	echo tag('h1', lang('Misc - mass_mail', 'title')), make_form(array(
		array('subject', lang('mail.subject') . tag('br')),
		array('body', tag('br') . lang('content'), 'textarea'),
	));
}

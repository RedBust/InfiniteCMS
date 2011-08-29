<?php
$q = empty($acc->question) ? 'DELETE?' : $acc->question;
$r = empty($acc->reponse) ? 'DELETE' : $acc->reponse;
$email = empty($acc->email) ? '@hotmail.fr' : $acc->email;

$newl = tag('br');
$auth = !$acc->exists() || level(LEVEL_ADMIN);

$fields = array(
	lang('acc.register.infos') => array(
		( $auth ? array('account', lang('account') . $newl, NULL, $acc->account) : array()),
		array('pass', ( $auth ? $newl : '' ) . lang('acc.register.password') . $newl, 'password', $acc->pass),
		array('pseudo', $newl . lang('pseudo') . $newl, NULL, $acc->pseudo),
		array('email', $newl . lang('acc.register.mail') . $newl, NULL, $email),
	),
	lang('acc.register.prefs') => array(
		array( 'question', lang( 'acc.register.question' ) . $newl, NULL, $q ),
		array( 'reponse', $newl . lang( 'acc.register.answer' ) . $newl, NULL, $r ),
		array( 'guid', NULL, 'hidden', $acc->guid ),
	),
	lang('acc.register.adv') => ( level(LEVEL_ADMIN) ? array(
		array('banned', lang('acc.register.banned') . $newl, 'checkbox', intval($acc->banned)),
		array('level', $newl . lang('level') . $newl, 'select', Member::getLevels(), $acc->level)
	) : array() )
);

if (!$acc->exists())
	array_unshift($fields, array('tos', lang('acc.register.accept_tos'), 'checkbox'));

echo make_form($fields);
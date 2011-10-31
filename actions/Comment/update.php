<?php
if (!check_level(LEVEL_MODO))
	return;

$router->codeUnless(404, $com = CommentTable::getInstance()->find($id = $router->requestVar('id', -1)));

$comTable = $com->getTable();
/* @var $comTable CommentaireTable */
$headers && print lang('updating_cols');
$col = $router->requestVar('col', array());
if (!in_array($col, array('title', 'content')) || empty($_POST['update_value']))
	$col = array('title', 'content');
else
	$vals = array($col => $_POST['update_value']);
foreach ((array) $col as $c)
{
	if (empty($vals[$c]))
		$errors[] = sprintf(lang('must_!empty'), $c);
	else
		$com->$c = $vals[$c];
}
$com->save();
if (!empty($col) && !is_array($col) && !$output) //AJaX
	exit($com->$col);

redirect(array(
	'controller' => $router->getController(),
	'action' => 'show',
	'id' => $com->News->id,
));
<?php
if (!check_level(LEVEL_ADMIN))
	return;

if (!($news = NewsTable::getInstance()->find($id = $router->requestVar('id', -1))))
{
	$title = lang($router->getController() . ' - create', 'title');
	$news = new News;
}
/* @var $news News */

if (!empty($_POST))
{
	$col = $router->requestVar('col', '');
	$vals = $_POST;
	if (!$news->getTable()->hasColumn($col) || empty($_POST['update_value']))
		$col = array();
	else
		$vals = array($col => $_POST['update_value']);
	$errors = $news->update_attributes($vals, $col);
	if (!empty($col) && !is_array($col))
		exit(nl2br($news[$col]));
}
if (count($_POST) < 1 || $errors != array())
{
	partial('_form', array('news'), PARTIAL_CONTROLLER);
}
elseif (count($_POST) > 0 && $errors == array())
{
	Cache::destroyPrefix($router->getController());
	if ($headers)
	{
		echo lang('news.saved') . make_link('@root', lang('back_to_index'));
		redirect(array('controller' => $router->getController(), 'action' => 'show', 'id' => $news['id']));
	}
}
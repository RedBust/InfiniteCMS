<?php
$vars = array(
	'{server.name}' => $config['SERVER_NAME'],
	'{server.corp}' => $config['SERVER_CORP'],
);
$f = 'assets/_shared/txt/fr/server.txt';

$update = false;
if (level(LEVEL_ADMIN))
{
	if ($router->requestVar('update'))
	{
		if ($router->isPost() && $c = $router->postVar('content'))
		{
			if (!$content = json_decode($c, true))
				return;

			file_put_contents($f, $content['server']['value']);
			exit;
		}
		else
		{
			stylesheet_tag('mercury.bundle', 'mercury');
			javascript_tag('Mercury/core', 'Mercury/dialogs', 'Mercury/loader');

			echo tag('h3', lang('variables')), tag('ul');
			foreach ($vars as $var => $value)
			{
				echo tag('li', tag('b', $var) . ' (' . $value . ')');
			}
			echo '</ul>';
			$update = true;
		}
	}
	else
		echo make_link(array('controller' => $router->getController(), 'action' => $router->getAction(), 'update' => true), lang('act._edit'), array(), array(), false);
}
$file = file_get_contents($f);
echo tag('div', array('id' => 'server', 'class' => 'mercury-region', 'data' => array('type' => 'editable')),
 $update ? $file : strtr($file, $vars));
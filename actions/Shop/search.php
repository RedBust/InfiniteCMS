<?php
load_models('static');
switch ($mode = $router->requestVar('mode'))
{
	case 'box': //show the box
		echo js(jQ()), tag('b', lang('shop.check_to_filter'))
		  . make_form(array(
				array('e_name', NULL, 'checkbox', '1', array(), false),
				array('name', '&nbsp;' . lang('shop.item.name') . '&nbsp;'),
				array('e_cost', NULL, 'checkbox', '1', array(), false),
				array('cost', '&nbsp;'
				 . sprintf(lang('shop.cost'), $config['POINTS_CREDIT'], $config['POINTS_VOTE']) . ', '
				 . lang('between') . '&nbsp;',
				 NULL, 0, array(), false),
				array('cost2', '&nbsp;' . lang('and') . '&nbsp;'),
			), to_url(array( 'controller' => $router->getController(), 'action' => 'search')));
		break;
	default:
		$table = ShopItemTable::getInstance();
		/* @var $table ItemTable */

		$itemsDql = Query::create()
						->from('ShopItem');
		$pager = new Doctrine_Pager($itemsDql, $router->requestVar('page'), $config['ITEM_LINES_BY_PAGE'] * $config['ITEMS_BY_LINE']);
		$filters = $search_params = array(); //search_params is used for pagination
		foreach( array( 'name', 'cost', 'cost2' ) as $type )
		{ //foreach columns
			$value = $router->requestVar( $type, NULL );
			if (( $type == 'cost2' || $router->requestVar('e_' . $type, NULL) !== NULL ) //e => enable
			 && !empty($value))
			{
				$search_params['e_' . $type] = '1'; //used for ...
				$search_params[$type] = $value; //... pagination
				if ($type == 'cost')
				{
					$sign = '>=';
				}
				else if ($type == 'cost2')
				{ //ie : cost 200 cost2 300 (in the form) => cost > 200 & < 300
					$type = 'cost';
					$sign = '<=';
				}
				else
				{
					$sign = 'LIKE';
					$filters[$type] = '%'.str_replace('*', '%', $value).'%';
				}
				$itemsDql->andWhere(sprintf('%s %s ?', $type, $sign), $value);
			}
		}

		$items = $pager->execute();
		$url_ary = array('controller' => $router->getController(), 'action' => $router->getAction()) + $search_params + array('page' => '');
		$layout = new Doctrine_Pager_Layout($pager, new Doctrine_Pager_Range_Sliding(array('chunk' => 4)), to_url($url_ary) . '{%page_number}');
		$layout->setTemplate('[<a href="{%url}">{%page}</a>]');
		$layout->setSelectedTemplate('[<b>{%page}</b>]');
		$items->shopDisplay();
		$layout->display();
}
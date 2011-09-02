<?php
load_models('static');
/* @var $table ItemTable */
$table = ShopItemTable::getInstance();

if ($router->requestVar('mode') == 'search')
{
	echo $table->getSearchBox();
	return;
}

$itemsDql = Query::create()
				->from('ShopItem si')
					->leftJoin('si.Effects e');
$search_params = array(); //search_params is used for pagination
$filter_names = $table->getFilters();
foreach ($filter_names as $type)
{ //foreach columns
	$is_check = substr($type, 0, 3) == 'is_'; //it's a check "is".
	$value = $router->requestVar($type);
	if (($router->requestVar('e_' . rtrim($type, '2')) !== NULL || $is_check)
	 && !empty($value)) //e = enabled
	{
		$value = $type == 'name' ? $value : ( $is_check ? strtolower($value) == 'on' : intval($value) );

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
		else if ($type == 'name')
		{
			$sign = 'LIKE';
			$value = '%'.str_replace('*', '%', $value).'%';
		}
		else
		{
			$sign = '=';
		}

		if ($type == 'cost' && level(LEVEL_VIP))
			$type = 'cost_vip';

		$itemsDql->andWhere(sprintf('%s %s ?', $type, $sign), $value);
	}
	if ($is_check)
		$itemsDql->addOrderBy($type . ' DESC');
}
foreach ($table->getProtectedFilters() as $filter)
{ //protected remaining filters (can NOT be used)
	$itemsDql->andWhere($filter . ' = 0');
}

$pager = new Doctrine_Pager($itemsDql, $router->requestVar('page'), $config['ITEM_LINES_BY_PAGE'] * $config['ITEMS_BY_LINE']);
$items = $pager->execute();
$url_ary = array('controller' => $router->getController(), 'action' => $router->getAction()) + $search_params + array('page' => '');
$layout = new Doctrine_Pager_Layout($pager, new Doctrine_Pager_Range_Sliding(array('chunk' => 4)), to_url($url_ary) . '{%page_number}');
$layout->setTemplate('[<a href="{%url}">{%page}</a>]');
$layout->setSelectedTemplate('[<b>{%page}</b>]');
if ($pager->haveToPaginate())
	$layout->display();

if ($items->count() || count($search_params))
{
	$i = 0; //where are we in da items foreash ?
	$items->shopDisplay();
	if ($pager->haveToPaginate())
		echo paginate($layout);

	//not used in case of any objects
	$search_url = array(
		'controller' => $router->getController(),
		'action' => $router->getAction(),
		'mode' => 'search',
	);
	foreach ($filter_names as $filter_name)
	{
		if (isset($search_params[$filter_name]))
			$searchUrl[$filter_name] = $search_params[$filter_name];
	}
	jQ('
searchItem = $( "#searchItem" );
searchItem.dialog(
	{
		autoOpen: false,
		draggable: true,
		resizable: true,
		width: 600,
	} );
needRefresh = true;
function searchItem_update(cnt)
{
	searchItem.dialog( "open" ).html( cnt );
}
function searchItem_init()
{
	if (needRefresh)
	{
		$.ajax(
		{
			url: "' . to_url($search_url + array('header' => 0), false) . '",
			success: function (data)
			{
				searchItem_update(data);
				searchItem.find("form").submit(function (event)
				{
					if ($(this).find(":checked").length == 0)
					{
						errorDiv
							.html("' . lang('shop.need_filter') . '")
							.dialog("open");
						event.preventDefault();
						return;
					}
				} );
			}
		} );
		needRefresh = false;
	}
	else
		searchItem.dialog( "open" );
}');
	echo tag('br') . js_link('searchItem.dialog("open");', tag('h1', lang('shop.item.search')), to_url($search_url)) .
	 tag('div', array(
		'style' => 'display: none;',
		'id' => 'searchItem',
		'title' => lang($router->getController() . ' - search', 'title'),
	 ), $table->getSearchBox());
}
else
{
	echo tag('p', array('align' => 'center'), lang('shop.any_objects'));
}

if (level(LEVEL_ADMIN))
{
	echo tag('br') . make_link(array('controller' => $router->getController(), 'action' => 'update'), lang('act.new'));
}

$items->free(true);
$itemsDql->free(true);
unset($table, $items, $pager, $layout, $itemsDql);
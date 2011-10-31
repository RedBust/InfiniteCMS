<?php
load_models('static');
/* @var $table ItemTable */
$table = ShopItemTable::getInstance();

if ($router->requestVar('mode') == 'search')
{
	echo $table->getSearchBox();
	return;
}
$raw = $router->isAjax() ? (bool) $router->requestVar('raw', false) : false;
$categories = Query::create()
					->from('ShopCategory sic INDEXBY id')
					->execute();

if (count($categories))
{
	$category = $categories->contains($cat = intval($router->requestVar('cat'))) ? $categories[$cat] : $categories->getFirst();
	$allItemsDql = Query::create()
					->from('ShopItem');
	$itemsDql = Query::create()
					->from('ShopItem si')
					->where('1=1')
						->leftJoin('si.Effects e');

	$search_params = array(); //search_params is used for pagination
	$filter_names = $table->getFilters();
	foreach ($filter_names as $type)
	{ //foreach columns
		$is_check = substr($type, 0, 3) == 'is_'; //it's a check "is".
		$value = $router->requestVar($type);
		$real_type = rtrim($type, '2'); //this is the REAL type. used for e_ ! important
		if (( $router->requestVar('e_' . $real_type) !== NULL || $is_check )
		 && !empty($value) && $value != -1) //e = enabled
		{
			$value = $type == 'name' ? $value : ( $is_check ? strtolower($value) == 'on' : intval($value) );

			$search_params['e_' . $real_type] = '1'; //used for ...
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

			if ($type == 'cat')
				$type = 'category_id';
			if ($type == 'cost' && (level(LEVEL_VIP) && !empty($config['COST_VIP'])))
				$type = 'cost_vip';

			$allItemsDql->andWhere(sprintf('%s %s ?', $type, $sign), $value);
			$itemsDql->andWhere(sprintf('si.%s %s ?', $type, $sign), $value);
		}
		if ($is_check)
		{
			$allItemsDql->addOrderBy($type . ' DESC');
			$itemsDql->addOrderBy('si.' . $type . ' DESC');
		}
	}
	foreach ($table->getProtectedFilters() as $filter)
	{ //protected remaining filters (can NOT be used)
		$allItemsDql->andWhere($filter . ' = 0');
		$itemsDql->andWhere($filter . ' = 0');
	}

	$allItems = $allItemsDql->fetchArray();
	$allowCats = array();
	foreach ($allItems as $allItem)
	{
		$allowCats[] = $allItem['category_id'];
	}
	$allowCats = array_unique($allowCats);

	if (!in_array($category->id, $allowCats)) //fallback to the first allowed cat. wat if it's empty =( ?
		$category = isset($allowCats[0]) ? $categories[$allowCats[0]] : NULL;

	$url_ary = array_merge(array('controller' => $router->getController(), 'action' => $router->getAction()), $search_params);
	if (count($allowCats) > 0)
	{
		$url_ary['cat'] = $category->id;

		if (!$raw)
		{
			echo '
	<div id="categories">
		<ul>';
			$selected = 0;
			$hasSelected = false;
			foreach ($allowCats as $cat)
			{
				$cat = $categories[$cat];
				$cat_url = to_url(array_merge($url_ary, array('cat' => $cat->id, 'raw' => 1, 'ajaxData' => 0)));

				if (!$hasSelected)
				{
					if ($cat->id == $category->id)
						$hasSelected = true;
					else
						++$selected;
				}

				echo tag('li', $cat->id == $category->id ? tag('a', array('href' => '#cat-' . $cat->id), $cat->getName()) :
				  make_link($cat_url, $cat->getName(), array(), array('data-href' => $cat_url), false));
			}
			if ($selected >= count($allowCats))
				$selected = count($allowCats) - 1;

			echo '
		</ul>
		<div id="cat-' . $category->id . '">';
			jQ('
	var categories = $("#categories");
	categories.tabs({selected: ' . $selected . '});');
		}
	}

	if ($category !== NULL)
		$itemsDql->andWhere('si.category_id = ?', $category->id);
	$pager = new Doctrine_Pager($itemsDql, $router->requestVar('page'), $config['ITEM_LINES_BY_PAGE'] * $config['ITEMS_BY_LINE']);
	$items = $pager->execute();
	$layout = new Doctrine_Pager_Layout($pager, new Doctrine_Pager_Range_Sliding(array('chunk' => 4)), to_url($url_ary + array('page' => '')));
	$layout->setTemplate('[<a href="{%url}">{%page}</a>]');
	$layout->setSelectedTemplate('[<b>{%page}</b>]');
	echo paginateLayout($layout);

	//not used in case of any (all)objects
	$search_url = array(
		'controller' => $router->getController(),
		'action' => $router->getAction(),
		'mode' => 'search',
	);

	if ($items->count() || count($search_params))
	{
		$i = 0; //where are we in da items foreash ?
		$items->shopDisplay();
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
	}
	else
	{
		echo tag('p', array('align' => 'center'), lang('shop.any_objects'));
	}

	if (!$raw)
	{
		if (!empty($allowCats))
			echo '
	</div>
</div>';

	if (count($allItems))
		echo tag('br') . js_link('searchItem.dialog("open");', tag('h1', lang('shop.item.search')), to_url($search_url)) .
		 tag('div', array(
			'style' => 'display: none;',
			'id' => 'searchItem',
			'title' => lang($router->getController() . ' - search', 'title'),
		 ), $table->getSearchBox());
	}

	$items->free(true);
	$itemsDql->free(true);
	if ($category !== NULL)
		$category->free(true);
	unset($table, $items, $pager, $layout, $itemsDql, $category);
}

if (level(LEVEL_ADMIN) && !$raw)
{
	if ($categories->count())
		echo tag('br') . make_link(array('controller' => $router->getController(), 'action' => 'update'), lang('act.new'));
	echo tag('br') . make_link(array('controller' => 'ShopCategory', 'action' => 'index'), lang('ShopCategory - index', 'title'));
}
$categories->free(true);
unset($categories);
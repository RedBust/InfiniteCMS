<?php
load_models('static');

$table = ShopItemTable::getInstance();
/* @var $table ItemTable */
$itemsDql = Query::create()
				->from('ShopItem si')
					->leftJoin('si.Effects ie');
$pager = new Doctrine_Pager($itemsDql, $router->requestVar('id'), $config['ITEM_LINES_BY_PAGE'] * $config['ITEMS_BY_LINE']);
$items = $pager->execute();
/* @var $items Collection */
$layout = new Doctrine_Pager_Layout($pager, new Doctrine_Pager_Range_Sliding(array('chunk' => 4)), to_url(array('controller' => $router->getController(), 'action' => $router->getAction(), 'id' => '')) . '{%page_number}');
$layout->setTemplate('[<a href="{%url}">{%page}</a>]');
$layout->setSelectedTemplate('[<b>{%page}</b>]');
$count = count($items); //is that last ?

if ($count)
{
	$i = 0; //where are we in da items foreash ?
	$items->shopDisplay();
	$layout->display();

	//not used in case of any objects
	$searchUrl = array(
		'controller' => $router->getController(),
		'action' => 'search',
		'mode' => 'box',
	);
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
	if( needRefresh )
	{
		$.ajax(
			{
				url: "' . to_url($searchUrl + array('header' => 0), false) . '",
				success: function (data)
				{
					searchItem_update( data );
					searchItem.find( "form" ).submit( function (event)
						{
							if( $( this ).find( ":checked" ).length == 0 )
							{
								errorDiv
									.html( "' . lang('shop.need_filter') . '" )
									.dialog( "open" );
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
	$table->getAutoComplete();
	echo tag('br') . js_link('searchItem_init();', tag('h1', lang('shop.item.search')), to_url($searchUrl)) .
	 tag('div', array(
		'style' => 'display: block;',
		'id' => 'searchItem',
		'title' => lang($router->getController() . ' - search', 'title'),
	 ), '');
}
else
{
	echo tag('p', array('align' => 'center'), lang('shop.any_objects'));
}

if (level(LEVEL_ADMIN))
{
	echo tag('br') . make_link(array('controller' => $router->getController(), 'action' => 'update'), lang('act.new'));
}
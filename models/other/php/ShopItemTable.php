<?php

/**
 * ItemTable
 *
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-Doc <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: ItemTable.php 24 2010-10-22 11:46:07Z nami.d0c.0 $
 */
class ShopItemTable extends RecordTable
{
	public function getNumericCols()
	{
		return array('cost', 'cost_vip', 'value');
	}
	public function getAllFilters()
	{
		return array('name', 'cost', 'cost2', 'is_lottery', 'is_vip', 'is_hidden');
	}
	public function getFilters()
	{
		$filters = array('name', 'cost', 'cost2', 'is_lottery');
		if (level(LEVEL_VIP))
			$filters[] = 'is_vip';
		if (level(LEVEL_ADMIN))
			$filters[] = 'is_hidden';
		return $filters;
	}
	public function getProtectedFilters()
	{
		return array_diff($this->getAllFilters(), $this->getFilters());
	}

	public function getSearchBox()
	{
		global $router, $config;
		$search_val = array();
		foreach ($this->getFilters() as $filter)
		{
			$search_val[$filter] = $router->requestVar($filter) ?: '';
		}

		$options = tag_open('div', array('id' => 'options')) . '>';
		$options .= input('is_lottery', lang('shop.is_lottery'), 'checkbox', $search_val['is_lottery']);
		if (in_array('is_vip', $this->getFilterNames()))
			$options .= input('is_vip', lang('shop.is_vip'), 'checkbox', $search_val['is_vip']);
		if (in_array('is_hidden', $this->getFilterNames()))
			$options .= input('is_hidden', lang('shop.is_hidden'), 'checkbox', $search_val['is_hidden']);
		$options .= '</div>';
		jQ('$("#options").buttonset();');

		return tag('b', lang('shop.check_to_filter'))
		  . make_form(array(
				array('e_name', NULL, 'checkbox', '1', array(), false),
				array('name', '&nbsp;' . lang('shop.item.name') . '&nbsp;', NULL, $search_val['name']),
				array('e_cost', NULL, 'checkbox', '1', array(), false),
				array('cost', '&nbsp;'
				 . sprintf(lang('shop.cost_simple'), $config['POINTS_CREDIT' . (level(LEVEL_VIP) ? '_VIP' : '')], $config['POINTS_VOTE' . (level(LEVEL_VIP) ? '_VIP' : '')]) . ', '
				 . lang('between') . '&nbsp;',
				 NULL, intval($search_val['cost']), array(), false),
				array('cost2', '&nbsp;' . lang('and') . '&nbsp;', NULL, intval($search_val['cost2'])),
				$options
			), APPEND_FORM_TAG);
	}
}
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
	protected $numericCols = array('cost', 'value', 'category_id'),
		$extFilters = array('name', 'cost', 'cost2', 'cat', 'is_lottery', 'is_hidden'), //extended
		$simpleFilters = array('name', 'cat', 'cost', 'cost2', 'is_lottery');
	public function getNumericCols()
	{
		global $config;
		if (empty($config['COST_VIP']))
			return $this->numericCols;
		else
			return array_merge($this->numericCols, array('cost_vip'));
	}
	public function getAllFilters()
	{
		global $config;
		if (empty($config['COST_VIP']))
			return $this->extFilters;
		else
			return array_merge($this->extFilters, array('is_vip'));
	}
	public function getFilters()
	{
		global $config;
		$filters = $this->simpleFilters;
		if (level(LEVEL_VIP) && !empty($config['COST_VIP']))
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
		if ($search_val['cat'] === '')
			$search_val['cat'] = -1;

		$options = tag_open('div', array('id' => 'options')) . '>';
		$options .= input('is_lottery', lang('shop.is_lottery'), 'checkbox', $search_val['is_lottery']);
		if (in_array('is_vip', $this->getFilters()))
			$options .= input('is_vip', lang('shop.is_vip'), 'checkbox', $search_val['is_vip']);
		if (in_array('is_hidden', $this->getFilters()))
			$options .= input('is_hidden', lang('shop.is_hidden'), 'checkbox', $search_val['is_hidden']);
		$options .= '</div>';
		jQ('$("#options").buttonset();');

		return tag('b', lang('shop.check_to_filter'))
		  . make_form(array(
			 array('e_name', NULL, 'checkbox', '1', array(), false),
			 array('name', '&nbsp;' . lang('shop.item.name') . '&nbsp;', NULL, $search_val['name']),
			 array('e_cat', NULL, 'checkbox', '1', array(), false),
			 array('cat', '&nbsp;' . lang('category'), 'record', array('empty' => true, 'type' => 'one', 'model' => 'ShopCategory'), $search_val['cat']),
			 array('e_cost', NULL, 'checkbox', '1', array(), false),
			 array('cost', '&nbsp;' .
			  sprintf(lang('shop.cost_simple'), $config['POINTS_CREDIT' . (level(LEVEL_VIP) && !empty($config['COST_VIP']) ? '_VIP' : '')], $config['POINTS_VOTE' . (level(LEVEL_VIP) ? '_VIP' : '')]) . ', ' .
			  lang('between') . '&nbsp;',
			  NULL, intval($search_val['cost']), array(), false),
			 array('cost2', '&nbsp;' . lang('and') . '&nbsp;', NULL, intval($search_val['cost2'])),
			 $options
			));
	}

	static public function getType($type)
	{
		global $types;
		foreach ($types as $t => $k)
		{
			if (is_array($k))
			{
				foreach ($k as $i => $v)
				{
					if ($i == $type)
						return $v;
				}
			}
			else if ($t == $type)
				return $k;
		}
		return NULL;
	}
}
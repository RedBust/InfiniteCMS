<?php

/**
 * ShopItem
 *
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-Doc <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: Item.php 33 2010-10-31 20:45:35Z nami.d0c.0 $
 */
class ShopItem extends BaseShopItem
{
	public function giveTo(Character $p)
	{
		if (!$this->Effects->count())
			return;

		if ($this->is_lottery)
		{
			$effect = $this->Effects[rand(0, $this->Effects->count()-1)];
			$effect->giveTo($p);
			return $effect;
		}
		else
		{
			foreach ($this->Effects as $effect)
				$effect->giveTo($p);
		}
	}

	/**
	 * updates shop item
	 *
	 * @param arrray $values Les valeurs (_POST)
	 * @access public
	 * @return array errors if errors ...
	 */
	public function update_attributes(array $values, $columns = NULL)
	{
		global $types, $config;

		$errors = array();
		if( $columns === NULL ) //WHERE vip & hidden ? for VIP-destined draw.
			$columns = array('description', 'name', 'cost', 'category_id', 'cost_vip', 'is_lottery', 'is_hidden');
		if (!empty($config['COST_VIP']))
			$columns[] = 'is_vip';
		if( is_string( $columns ) )
			$columns = explode( ';', $columns );
		$prev = $this->exists() ? $this->toArray() : array();

		foreach ((array)$columns as $t)
		{
			$t = lcfirst($t);
			//check if the value is valid
			if (( !isset($values[$t]) || (isset($values[$t]) && trim($values[$t]) === '' ) )
			 && substr($t, 0, 3) !== 'is_')
			{
				$errors[$t] = sprintf(lang('must_!empty'), $t);
			}
			else
			{
				if (substr($t, 0, 3) == 'is_')
				{
					$values[$t] = isset($values[$t]) && ( $values[$t] == 'on' || $values[$t] == '1' );
				}

				if (in_array($t, $this->getTable()->getNumericCols()) && strval(intval($values[$t])) !== $values[$t])
					$errors[$t] = sprintf(lang('must_numeric'), $t);
				else
					$this->$t = $values[$t];
			}
		}
		if (!$this->relatedExists('Category'))
			$errors[] = sprintf(lang('must_!empty'), lang('category'));
		if (!empty($config['COST_VIP']) && $this->cost < $this->cost_vip)
		{
			if (empty($prev['cost_vip']))
				$this->cost_vip = $this->cost - 1; //shouldn't eq:0
			else
			{
				if ($this->cost > $prev['cost_vip'])
					$this->cost = $prev['cost'];

				$this->cost_vip = $prev['cost_vip'];
			}	
			$errors[] = lang('shop.cost_vip_lower');
		}
		if (!empty($values['type']) && !empty($values['value']))
		{
			$table = ShopItemEffectTable::getInstance();
			/* @var $table ShopItemEffectTable */
			foreach ($values['type'] as $id => $type)
			{
				if (empty($values['value'][$id]))
					continue;
				else if(!$this->getTable()->getType($type) && $type != -1)
					$errors[] = sprintf(lang('must_numeric'), 'type (' . $id . ')' );
				else if ($this->Effects->contains($id))
				{
					if ($type === NULL || $type == -1)
						$this->Effects[$id]->delete(); //remove the effect
					else
					{
						$this->Effects[$id]->type = $type;
						if (isset($values['value'][$id]) && $this->Effects[$id]->setValue($values['value'][$id]))
							$this->Effects[$id]->save();
						else
							$errors['effect_' . $id] = sprintf(lang('must_!empty'), $t);
					}
				}
				else
				{
					if ($type === NULL || $type == -1)
						continue; //don't add blank effects

					$effect = new ShopItemEffect;
					$effect->type = $type;
					if ($effect->setValue($values['value'][$id]))
					{
						$effect->save();
						$this->Effects->add($effect);
					}
					else
						$errors['effect_' . $id] = sprintf(lang('must_!empty'), $t);
				}
			}
		}
		if ($errors == array())
		{
			$this->save();
		}

		return $errors;
	}

	public function __toString()
	{
		global $account;

		$id = $this->getDataId();

		$effects = '';
		if ($this->Effects->count())
		{
			$effects = '
			<ul>';
			foreach ($this->Effects as &$effect)
			{
				$effects .= tag('li', $effect);
			}
			$effects .= '
			</ul>';
		}
		$desc = trim(News::format($this->description));

		return sprintf('
			<b>%s:</b> %s.<br />
			%s<b>%s:</b><br />%s<br /><!-- desc -->
			%s<br /><!-- cost(s) -->
			%s<!-- type(s) -->
			%s<!-- Effects -->
			%s
			%s',
		 lang('name'), tag('span', $id + array('class' => 'f_name'), $this->name),
		 empty($desc) ? '<!--' : '', lang('desc'), $desc,
		 $this->getCostInfo(),
		 $this->getTypesInfo(),
		 $effects,
		 ( level(LEVEL_LOGGED) && $account->User->canPurchase($this) ? $this->getPurchaseLink() : '' ),
		 ( level(LEVEL_ADMIN) ? tag('br') . $this->getUpdateLink() . '<br />' . $this->getDeleteLink() : '' ));
	}

	public function getDataId()
	{
		return array('data-id' => $this->id);
	}
	public function getTypesInfo()
	{
		global $router, $config;;
		if ($router->getController() != 'Shop')
			return ''; //this is a hack, I know ._.

		$types = array();
		if ($this->is_vip && !empty($config['COST_VIP']))
			$types[] = lang('shop.is_vip');
		if ($this->is_lottery)
			$types[] = lang('shop.is_lottery');
		if ($this->is_hidden && level(LEVEL_ADMIN))
			$types[] = lang('shop.is_hidden');

		return implode(' &bull; ', $types);
	}
	public function getCost()
	{
		global $config;
		if (level(LEVEL_VIP) && !empty($config['COST_VIP']))
			return $this->cost_vip;
		return $this->cost;
	}
	public function getName()
	{
		return $this->name;
	}
	public function getCostInfo()
	{
		global $config;

		$cost = '';
		$id = $this->getDataId();

		if (level(LEVEL_VIP) && !empty($config['COST_VIP']))
		{
			$cost .= tag('b', lang('cost_vip') . ' : ') . pluralize(lang('point'), $this->cost_vip, true, tag('span', $id + array('class' => 'f_cost_vip'), '%%content%%'));
		}
		if (empty($config['COST_VIP']) || !$this->is_vip)
		{
			if (level(LEVEL_ADMIN))
				$cost .= tag('br');
			if (!level(LEVEL_VIP) || level(LEVEL_ADMIN))
			{
				$cost .= tag('b', lang('cost') . ' : ') . pluralize(lang('point'), $this->cost, true, tag('span', $id + array('class' => 'f_cost'), '%%content%%'));
			}
		}
		return $cost;
	}

	public function getPurchaseLink()
	{
		return make_link(array('controller' => 'Shop', 'action' => 'purchase', 'id' => $this->id), lang('act.choose'));
	}
	public function getUpdateLink()
	{
		return make_link(array('controller' => 'Shop', 'action' => 'update', 'id' => $this->id), lang('act.edit'));
	}
	public function getDeleteLink()
	{
		return make_link(array('controller' => 'Shop', 'action' => 'delete', 'id' => $this->id), lang('act.delete_item'));
	}
}

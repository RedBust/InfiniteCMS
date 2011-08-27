<?php

/**
 * Item
 *
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-Doc <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: Item.php 33 2010-10-31 20:45:35Z nami.d0c.0 $
 */
class ShopItem extends BaseShopItem
{
	/**
	 * updates shop item
	 *
	 * @param arrray $values Les valeurs (_POST)
	 * @access public
	 * @return array errors if errors ...
	 */
	public function update_attributes(array $values, $columns = NULL)
	{
		global $types;
		$errors = array();
		if( $columns === NULL ) //WHERE vip & hidden ? for VIP-destined draw.
			$columns = array('description', 'name', 'cost', 'cost_vip', 'is_vip', 'is_lottery', 'is_hidden');
		if( is_string( $columns ) )
			$columns = explode( ';', $columns ); 

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
					$errors[$t] = sprintf(lang( 'must_numeric'), $t);
				else
					$this->$t = $values[$t];
			}
		}
		vdump($errors, $this->toArray(), $columns);
		if (!empty( $values['type'] ) && !empty( $values['value'] ) )
		{
			$table = ShopItemEffectTable::getInstance();
			/* @var $table ShopItemEffectTable */
			foreach( $values['type'] as $id => $type )
			{
				if (empty($values['value'][$id]))
					continue;
				else if( !isset( $types[$type] ) && $type != -1 )
					$errors['type'] = sprintf( lang( 'must_numeric' ), 'type (' . $id . ')' );
				else if( $this->Effects->contains($id) )
				{
					if( $type === NULL || $type == -1 )
						$this->Effects[$id]->delete(); //remove the effect
					else
					{
						$this->Effects[$id]->type = $type;
						if( isset( $values['value'][$id] ) && $this->Effects[$id]->setValue( $values['value'][$id] ) )
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
					if (!$effect->setValue($values['value'][$id]))
						$errors['effect_' . $id] = sprintf(lang('must_!empty'), $t);
					else
					{
						$effect->save();
						$this->Effects->add($effect);
					}
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
		global $m, $account;

		$id = array('data-id' => $objet['id']);

		$effects = '';
		if ($objet->Effects->count())
			$effects = '
		<ul>' . implode($this->Effects) . '
		</ul>';

		$html .= sprintf('
		<td%s>
			<b>%s:</b> %s.<br />
			<b>%s:</b><br />%s<br />
			%s <!-- cost(s) -->
			%s <!-- Effects -->
			%s
			%s
		</td>', ( $i === $count ? ' colspan="' . strval($config['ITEMS_BY_LINE'] - $m) . '"' : ''),
		 lang('name'), tag('span', $id + array('class' => 'f_name'), $objet['name']),
		 lang('desc'), News::format($objet['description']),
		 $this->getCostInfo(),
		 $effects,
		 ( $account->getMainChar() === NULL || $account->User->points < $this->getCost() ? '' : $this->getPurchaseLink()),
		 (!level(LEVEL_ADMIN) ? '' : tag('br') . $this->getUpdateLink() . '<br />' . $this->getDeleteLink()));
	}


	public function getCost()
	{
		if (level(LEVEL_VIP))
			return $this->cost_vip;
		return $this->cost;
	}
	public function getCostInfo()
	{
		$cost = '';
		if (level(LEVEL_VIP))
		{
			$cost .= tag('b', lang('cost_vip')) . pluralize(lang('point'), $this->cost_vip, true, tag('span', $id + array('class' => 'f_cost_vip'), '%%content%%'));
		}
		if (!$this->is_vip)
		{
			if (level(LEVEL_ADMIN))
				$cost .= tag('br');
			if (!level(LEVEL_VIP) || level(LEVEL_ADMIN))
			{
				$cost .= tag('b', lang('cost')) . pluralize(lang('point'), $this->cost, true, tag('span', $id + array('class' => 'f_cost'), '%%content%%'));
			}
		}
	}

	public function getPurchaseLink()
	{
		return make_link(array('controller' => $router->getController(), 'action' => 'purchase', 'id' => $objet['id']), lang('act.choose'));
	}
	public function getUpdateLink()
	{
		return make_link(array('controller' => $router->getController(), 'action' => 'update', 'id' => $objet['id']), lang('act.edit'));
	}
	public function getDeleteLink()
	{
		return make_link(array('controller' => $router->getController(), 'action' => 'delete', 'id' => $objet['id']), lang('act.delete_item'));
	}
}

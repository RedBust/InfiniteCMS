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
	 * maj
	 * Met à jour l'item
	 *
	 * @param arrray $values Les valeurs (_POST)
	 * @access public
	 * @return array errors if errors ...
	 */
	public function update_attributes(array $values, $columns = NULL)
	{
		global $types;
		$errors = array();
		if( $columns === NULL )
			$columns = array( 'name', 'cost', 'description' );
		if( is_string( $columns ) )
			$columns = explode( ';', $columns ); 

		foreach( (array)$columns as $t )
		{
			$t[0] = strtolower( $t[0] );
			//check if the value is valid
			if( !isset( $values[$t] ) || ( isset( $values[$t] ) && trim( $values[$t] ) === '' ) )
			{
				$errors[$t] = sprintf( lang( 'must_!empty' ), $t );
			}
			else
			{
				if( in_array( $t, array( 'cost', 'value' ) ) && strval( intval( $values[$t] ) ) !== $values[$t] )
					$errors[$t] = sprintf( lang( 'must_numeric' ), $t );
				else
					$this->$t = $values[$t];
			}
		}
		if( !empty( $values['type'] ) && !empty( $values['value'] ) )
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
}

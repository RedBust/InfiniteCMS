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
	const AUTOCOMPLETE_EXEC = true,
		AUTOCOMPLETE_RETURN = false;

	protected $_hasItemList = false;

	public function getAutoComplete($val = 'form_value', $exec = NULL)
	{
		if( !$this->_hasItemList )
		{
			$_items = lang( NULL, 'item' );
			$count = count( $_items );
			$items_txt = '';
			foreach( $_items as $i => $name )
			{
				$items_txt .= "'" . str_replace( '\'', '\\\'', $name ) . "', ";
			}
			$items_txt = ( $count ? substr( $items_txt, 0, -2 ) : $items_txt );
			jQ( 'var items = [' . $items_txt . '];' );
			$this->_hasItemList = true;
		}

		if( $val === NULL )
			return;
		$js = '
var _value = $( \'#' . $val . '\' );

_value.autocomplete(
	{
		source: items
	} );';
		if( $exec )
			jQ( $js );
		return $js;
	}
}
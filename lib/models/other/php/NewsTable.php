<?php

/**
 * NewsTable
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-Doc <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: NewsTable.php 24 2010-10-22 11:46:07Z nami.d0c.0 $
 */
class NewsTable extends RecordTable
{
	protected $panelsInit = false;

	public function initPanels()
	{
		if ($this->panelsInit)
			return;
		$this->panelsInit = true;

		echo tag('div', array(
			'title' => lang('News - update', 'title'),
			'id' => 'news_edit',
			'style' => 'display: none;'
		), '');
		jQ('
var newsPanel = $( "#news_edit" ), f = true;
newsPanel.dialog( $.extend( dialogOpt, {"modal": false } ) );
function newsEditPanel(id)
{
	var cont = $( "#form_content_ifr" ).find( "html > body" ).html();
	if( cont === "" || cont === null )
	{
		updateContent( locations[$( ".edit_link_" + id ).attr( "id" )] );
		return;
	}
	newsPanel.find( "div" ).hide();
	newsPanel.find( "#news_panel-" + id ).show();
	newsPanel.dialog( "open" );
	
	if( f )
	{
		f = false;
	}
}
pageBind(function ()
{
	newsPanel.dialog( "close" );
	delete newPanel;
	delete f;
});');
		define('FROM_INCLUDE', true);
	}
}
<?php

/**
 * GuildMemberTable
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-Doc <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: GuildMemberTable.php 24 2010-10-22 11:46:07Z nami.d0c.0 $
 */
class GuildMemberTable extends RecordTable
{
	protected $rightsPanel = false;

	public function initRightsPanel()
	{
		if ($this->rightsPanel)
			return;
		$this->rightsPanel = true;

		echo tag('div', array('id' => 'rightsPanel', 'style' => array('display' => 'none'), 'title' => lang('guild.rights')), '');
		jQ('
var rightsPanel = $("#rightsPanel").dialog(dialogOpt),
	rightsPanels = [];

function registerRightsPanel(rights)
{
	rightsPanels[rights] = $("#rights" + rights).hide().appendTo(rightsPanel);
}
function showRightsPanel(rights)
{
	rightsPanel.find(".rights").hide();
	rightsPanels[rights].show();
	rightsPanel.dialog("open");
}
pageBind(function ()
{
	rightsPanel.dialog("close");
	delete rightsPanel;
	delete rightsPanels;
});');
	}
}
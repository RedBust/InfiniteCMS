<?php

/**
 * Item
 *
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-Doc <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: News.php 24 2010-10-22 11:46:07Z nami.d0c.0 $
 */
class News extends BaseNews
{
	protected static $_panelsInit = false;

	/**
	 * format
	 * format a news after TinyMCE editing
	 *
	 * @access public
	 * @static
	 *
	 * @param string $content Content of the textarea
	 * @return string content cleared
	 */
	static public function format($content)
	{
		$content = str_replace(array('<p', '</p>'), array('<div', '</div>'), $content);
		$content = str_replace(array(
			'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
			'<html>',
			'<head>',
			'<title>Untitled document</title>',
			'</head>',
			'<body>',
			'</body>',
			'</html>',
				), '', $content);
		$content = preg_replace('`<div>(.*)</div>`', '$1<br />', $content);
		$content = str_replace("\n", '', $content);
		$content = str_replace('../../static', getPath() . 'static', $content);
		return $content;
	}

	/**
	 * updates the object
	 *
	 * @param array $values Les valeurs à mettre dans la news
	 * @return array
	 */
	public function update_attributes(array $values, $columns = array())
	{
		global $account;
		$errors = array();
		if ($columns === array())
			$columns = array('title', 'content');
		if (is_string($columns))
			$columns = explode(',', $columns);
		foreach ($columns as $t)
		{
			if (!$this->getTable()->hasColumn($t))
				continue;
			$t[0] = strtolower($t[0]);
			if (empty($values[$t]))
			{
				$errors[] = sprintf(lang('must_!empty'), $t);
			}
			else
				$this->$t = $values[$t];
		}
		$this->Author = $account->User;
		if ($errors == array())
		{
			$this->save();
		}

		return $errors;
	}

	protected static function _initPanels()
	{
		if (!self::$_panelsInit)
		{
			echo tag('div', array(
				'title' => lang('Index - update', 'title'),
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
		tinymce_include();
		f = false;
	}
}
bind( function () { newsPanel.dialog( "close" ); delete newPanel; delete f; } );');
			define('FROM_INCLUDE', true);
			self::$_panelsInit = true;
		}
	}

	public function buildLinks()
	{
		global $router, $config;
		$baseAction = array('controller' => $router->getController(), 'id' => $this['id']);
		$params = array(
			'show' => $baseAction + array('action' => 'show'),
			'update' => $baseAction + array('action' => 'update'),
			'delete' => $baseAction + array('action' => 'delete'),
		);
		$auth = level(LEVEL_ADMIN);
		if ($auth)
		{
			self::_initPanels();
			$news = $this; //temp for _form
			echo tag('div', array('id' => 'news_panel-' . $this['id'], 'class' => 'news-panel'), require $router->getPath($baseAction['controller'], '_form'));
			jQ('newsPanel.append( $( "#news_panel-' . $this['id'] . '" ) )', 'cache');
		}
		$title = tag('span', array('id' => 'newsTitle'), $this['title']);

		if ($config['LOAD_TYPE'] == LOAD_NONE)
			$edit_link = make_link($params['update'], lang('act._edit'));
		else
			$edit_link = js_link('newsEditPanel( ' . $this['id'] . ' )', lang('act._edit'), to_url($params['update']), array('class' => 'edit_link_' . $this['id']));

		return ( $router->getAction() == 'index' ? make_link($params['show'], $title) : $title ) .
		( $auth ?
				'  ' . $edit_link . ' ' .
				make_link($params['delete'], lang('act.delete')) : '' );
	}
}
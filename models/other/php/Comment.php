<?php

/**
 * Comment
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Vendethiel <vendethiel@hotmail.fr>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Comment extends BaseComment
{
	public function getDateInfo()
	{
		$date = explode(' ', $this->created_at);
		return sprintf(lang('at'), $date[0], $date[1]);
	}
	public function getCensorLink()
	{
		return make_link(array('controller' => $router->getController(), 'action' => 'comment', 'mode' => 'censor', 'id' => $this->id), lang('censor'), array(), array(), false);
	}

	public function __toString()
	{
		if ($this->relatedExists('Author') && $this->Author->relatedExists('Account'))
			$author = sprintf(lang('by'), make_link($this->Author->Account));
		else
			$author = '';

		return tag('h3', array('class' => 'comment-date'), '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .
		 tag('span', array('class' => 'comment-title'), $this->title) . ' - <i>' .
		 sprintf(lang('created'), $this->getDateInfo()) . $author . '</i>' .
		 ( level(LEVEL_ADMIN) ? $this->getCensorLink() : '' )) .
		tag('div', array('class' => 'comment-content', 'data-id' => $this['id'], 'style' => 'height: 220px !important;'),
		 nl2br(News::format($this->content))); //xss protection already done
	}
}
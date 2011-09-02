<?php

/**
 * USer
 *
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: Compte.php 53 2011-01-15 11:11:37Z nami.d0c.0 $
 */
class User
		extends BaseUser
{
	protected $pmUnreads = null;

	/**
	 * @package Doctrine
	 *
	 * @global array $config
	 * @param Doctrine_Event $event
	 * @return void
	 */
	public function preInsert(Doctrine_Event $event)
	{
		global $config;

		$inv = $event->getInvoker();
		// @FIXME add some checks from proxy'n'll
		$inv->lastIP = ip2long($_SERVER['REMOTE_ADDR']);
		$inv->culture = $config['use_lang'];
	}

	/**
	 * @return string Litteral representation of points
	 */
	public function getPoints()
	{
		return pluralize(ucfirst(lang('point')), $this->points) . ': ' .
		 tag('span', array('class' => 'f_points', 'data-id' => $this->guid), $this->points);
	}

	public function canVote(Poll $poll)
	{
#		vdump($poll->toArray(), $this->PollOptions->toArray());
		foreach ($this->PollOptions as $option)
		{ /* @var $option PollOption */
			if ($option->Poll->id == $poll->id)
				return false;
		}
		return true;
	}
	public function canPurchase(ShopItem $si)
	{
		if ($this->Account->level >= LEVEL_ADMIN)
			return true;
		if ($si->is_vip && !$this->Account->vip)
			return false;
		return $this->points >= $si->getCost();
	}
	public function canReview()
	{
		return level(LEVEL_ADMIN) || !$this->relatedExists('Review'); //if this can make happy admins \o/
	}
	public function canComment(News $news)
	{
		foreach ($this->Comments as $comment)
			if ($comment->News == $news)
				return false;
		return true;
	}

	/**
	 * returns unread private messages
	 */
	public function getUnreadPM()
	{
		if (null === $this->pmUnreads)
		{
			$this->pmUnreads = Query::create()
									->from('PrivateMessageThread pmt INDEXBY pmt.id')
										->leftJoin('pmt.Receivers pmr INDEXBY pmr.user_guid')
									->where('pmt.id IN (SELECT pmtr.thread_id FROM PrivateMessageThreadReceiver pmtr WHERE pmtr.user_guid = ? AND pmtr.next_page != 0)',
										 $this->guid)
									->execute();
		}
		return $this->pmUnreads;
	}
	public function getNextPMNotif()
	{
		$unreads = $this->getUnreadPM();
		if ($unreads->count())
		{
			$unread = $unreads->getFirst();
			$pm_url = to_url(array('controller' => 'PrivateMessage', 'action' => 'show', 'id' => $unread['id'], 'page' => $unread['Receivers'][$this->guid]['next_page']));
			return sprintf(lang('pm.arrived' . ($unreads->count() > 1 ? 's' : '')), $pm_url, $unreads->count()) . tag('br');
		}
		else
			return '';
	}
}
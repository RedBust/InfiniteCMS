<?php

/**
 * PrivateMessageAnswer
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Vendethiel <vendethiel@hotmail.fr>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class PrivateMessageAnswer extends BasePrivateMessageAnswer
{
	public function getDatesInfo()
	{
		$created = explode(' ', $this->created_at);
		return sprintf(lang('_the_at'), $created[0], $created[1]);
	}
	public function getPage()
	{
		if (!$this->exists())
			throw new LogicException('Can\'t paginate unexistant record');

		$prev = Query::create()
					->select('COUNT(pma.id) AS prev')
					->from('PrivateMessageAnswer pma')
					->where('pma.id < ?', $this->id)
						->andWhere('pma.thread_id = ?', $this->Thread->id)
					->fetchOneArray();
		$prev = $prev['prev'];
		return 1 + ($prev - ($prev % $config['PMA_BY_PAGE'])) / $config['PMA_BY_PAGE'];
	}
}
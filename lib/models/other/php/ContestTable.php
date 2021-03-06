<?php

/**
 * ContestTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class ContestTable extends RecordTable
{
	public function retrieve($var = 'contest')
	{
		global $router;

		return $this->createQuery('c')
						->leftJoin('c.Jurors j INDEXBY id')
							->leftJoin('j.Account ja')
						->leftJoin('c.Voters v INDEXBY id')
							->leftJoin('v.Account va')
						->leftJoin('c.Participants p INDEXBY character_id')
					->where('id = ?', $router->requestVar($var, -1))
					->fetchOne();
	}
}
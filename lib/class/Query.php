<?php

/**
 * adds more possibility to the basic Doctrine_Query class
 *
 * @file $Id: Query.php 20 2010-09-24 08:51:22Z nami.d0c.0 $
 *
 * @extends Doctrine_Query
 */
class Query extends Doctrine_Query
{
	protected $joinAlias = array();

	public function fetchOneArray($params = array())
	{
		return $this->fetchOne($params, Doctrine_Core::HYDRATE_ARRAY);
	}

	public static function toLike($sql)
	{
		return strtr($sql, array(
			'*' => '%',
			'%' => '*',
		));
	}

	public function addJoins($joins)
	{
		$model = $this->_dqlParts['from'][0][0];
		foreach ($joins as $join)
		{
			$col = explode('.', $join);
			$this->joinAlias[$col[1]] = strtolower($col[1][0]); //first letter of join name
			$this->leftJoin(sprintf('%s.%s %s', $model, $col[1], $this->joinAlias[$col[1]]));
		}
		return $this;
	}

	public function addChecks($checks)
	{
		foreach ($checks as $check)
		{
			$col = explode('.', $check[0]);
			$this->addWhere(sprintf('%s.%s %s ?', $this->joinAlias[$col[0]], $col[1], $check[1]), $check[2]);
		}
		return $this;
	}

	public function addJoinsAndChecks($joins, $checks)
	{
		return $this
				->addJoins($joins)
				->addChecks($checks);
	}
}
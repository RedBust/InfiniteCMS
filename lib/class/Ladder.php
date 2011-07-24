<?php

/**
 * represents a ladder
 *
 * @file $Id: Ladder.php 52 2010-12-22 16:57:08Z nami.d0c.0 $
 *
 * @abstract
 * @ignore
 */
class Ladder
{
	protected $_query = NULL,
	$_persos = NULL,
	$_adminLvl = LEVEL_MJ,
	$_allowAdmins = false,
	$_orderBy = 'xp',
	$_sens = 'DESC',
	$_limit = 20;

	public function __construct($orderBy = NULL, $fields = NULL, $allowAdmins = NULL, $sens = NULL, $limit = NULL)
	{
		if ($orderBy !== NULL)
			$this->setOrderBy($orderBy);
		if ($fields !== NULL)
			$this->setFields($fields);
		if ($allowAdmins !== NULL)
			$this->setAllowAdmins($allowAdmins);
		if ($sens !== NULL)
			$this->setSens($sens);
		if ($limit !== NULL)
			$this->setLimit($liimit);
	}

	protected function _getEndTag($td)
	{
		$posSpace = strpos(' ', $td);
		$posGT = strpos('>', $td);
		$pos = $posGT > $posSpace ? $posSpace : $posGT;
		$tdEnd = str_replace('<', '</', substr($td, 0, $pos));
		if (strpos('>', $tdEnd) === false)
			$tdEnd .= '>';
	}

	public function set(array $vals)
	{
		foreach ($vals as $key => $value)
			$this->{'set' . $key}($value);
	}

	public function getSens()
	{
		return $this->_sens;
	}

	public function setSens($value)
	{
		$this->_sens = $value;
		return $this;
	}

	public function getLimit()
	{
		return $this->_limit;
	}

	public function setLimit($value)
	{
		$this->_limit = $value;
		return $this;
	}

	public function getFields($td = '')
	{
		$fields = '';
		foreach ($this->_fields as $f)
		{
			$fields .= $td . tag('b', lang($f)) . $this->_getEndTag($td);
		}
		return $fields;
	}

	public function setFields($fields)
	{
		if (!is_array($fields))
			$fields = array($fields);

		$this->_fields = $fields;
		return $this;
	}

	public function getResultFields($td, $perso)
	{
		$fields = '';
		foreach ($this->_fields as $f)
		{
			$fields .= $td . $this->_dotSplit($f, $perso) . $this->_getEndTag($td);
		}
		return $fields;
	}

	public function getQuery()
	{
		$this->_calculateQuery();
		return $this->_query;
	}

	public function setQuery(Query $q)
	{
		$this->_query = $q;
		return $this;
	}

	public function getAllowAdmins()
	{
		return $this->_allowAdmins;
	}

	public function setAllowAdmins($value)
	{
		$this->_allowAdmins = $value;
		return $this;
	}

	public function getPersos()
	{
		return $this->_persos;
	}

	public function setPersos(array $persos)
	{
		$this->_persos = $persos;
		return $this;
	}

	public function getOrderBy()
	{
		return $this->_orderBy;
	}

	public function setOrderBy($orderBy)
	{
		$this->_orderBy = $orderBy;
		return $this;
	}

	protected function _calculateQuery()
	{
		if ($this->_query === NULL)
		{
			$q = Query::create()
					->select('p.*, a.*')
					->from('Characters p')
					->leftJoin('p.Account a')
					->where('a.banned = 0')
					->orderBy('p.' . $this->getOrderBy() . ' DESC')
					->limit($this->getLimit());
			if (!$this->getAllowAdmins())
				$q->andWhere('a.level = 0');

			$this->setQuery($q);
		}
	}

	protected function _hasAdminLevel()
	{
		return level($this->_adminLvl);
	}

	protected function _dotSplit($str, $perso)
	{
		$p = $perso;
		foreach (explode('.', $str) as $piece)
			$p = $p[$piece];
		return $p;
	}

	public function render()
	{
		$this->setPersos($this->getQuery()->execute());

		$perso = $this->getPersos();
		if (count($persos) > 0)
		{
			$td = '<td valign="center" align="center">';
			echo '
			<table border="1" style="width: 95%">
				<tr>
					' . $td . '
						<b>
							' . lang('acc.ladder.pos') . '
						</b>
					</td>
					' . $td . '
						<b>
							' . lang('character') . '
						</b>
					</td>
					' . $this->getFields($td) . '
					' . $td . '
						<b>
							' . lang('acc.ladder.guild') . '
						</b>
					</td>
					' . $td . '
						<b>
							' . lang('acc.ladder.class') . '
						</b>
					</td>
					' . $td . '
						<b>
							' . lang('acc.ladder.sex') . '
						</b>
					</td>
					' . ( $this->_hasAdminLevel() ? '
					' . $td . '
						<b>
							' . lang('account') . '
						</b>
					</td>' : '' ) . '
				</tr>';
			$td = '<td valign="center">';
			$i = 0;
			foreach ($persos as $perso)
			{
				$g = tag('i', lang('acc.no_guild'));
				if ($perso['GuildMember']['Guild'] !== NULL && $perso['GuildMember']['rank'] !== NULL) //lazy load
					$g = $perso['GuildMember']['Guild']['name'];
				$p = html($perso['name']);
				echo '
				<tr>
					' . $td . '
						' . ++$i . '
					</td>
					' . $td . '
						' . ( level(LEVEL_ADMIN) ? make_link(array('controller' => 'Account', 'action' => 'edit', 'id' => $perso['Account']['guid']), $p) : $p ) . '
					</td>
					' . $this->getResultFields($td, $perso) . '
					' . $td . '
						' . $g . '
					</td>
					' . $td . '
						' . IG::getClass($perso['class']) . '
					</td>
					' . $td . '
						' . IG::getSexe($perso['sexe']) . '
					</td>
					' . ( $this->_hasAdminLevel() ? '
					' . $td . '
						' . html($perso['Account']['account']) . '
					</td>' : '' ) . '
				</tr>';
			}
			echo '
			</table>';
		}
		else
		{
			echo tag('b', lang('acc.ladder.no_character'));
		}
	}

	public function __toString()
	{
		return $this->render();
	}
}
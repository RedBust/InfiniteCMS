<?php

/**
 * Map
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-Doc <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: Map.php 24 2010-10-22 11:46:07Z nami.d0c.0 $
 *
 * @property Collection Subareas
 */
class Map extends BaseMap
{
	protected $pos = array();

	protected function _posInit()
	{
		if( $this->pos === array() )
		{
			$pos = explode( ',', $this->mappos );
			$subarea = Query::create()
							->from( 'Subarea sb' )
								->leftJoin('sb.Area a')
								->where( 'id = ?', $pos[2] )
							->fetchOne();
			/* @var $subarea Subarea */
			$this->pos = array(
					'x' => $pos[0],
					'y' => $pos[1],
					'SubAreaID' => $pos[2],
					'SubArea' => $subarea,
					'AreaID' => $subarea->Area->id,
					'Area' => $subarea->Area,
				);
		}
	}

	public function getPosX()
	{
		$this->_posInit();
		return $this->pos['x'];
	}
	public function getPosY()
	{
		$this->_posInit();
		return $this->pos['x'];
	}
	public function getAreaId()
	{
		$this->_posInit();
		return $this->pos['areaID'];
	}
	public function getArea()
	{
		$this->_posInit();
		return $this->pos['area'];
	}
}
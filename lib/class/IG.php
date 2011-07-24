<?php

/**
 * manages IG-related things
 *
 * @version $Id: IG.php 52 2010-12-22 16:57:08Z nami.d0c.0 $
 *
 * @abstract
 */
abstract class IG
{
	protected static $statsMin = array(),
	$statsMax = array(),
	$reverseTable = array(
		'99' => '7d',
		'9c' => '7c',
		'9d' => '76',
		'9b' => '7e',
		'98' => '7b',
		'9a' => '77',
	);

	static public function getBreed($id)
	{
		return lang('breed.' . $id);
	}

	static public function getGender($id)
	{
		return lang('gender.' . $id);
	}

	static public function getStat($id, $type = 'max')
	{
		$stat = self::${'stats' . ucfirst($type)};
		return isset($stat[$id]) ? $stat[$id] : 0;
	}

	static public function resetStats()
	{
		self::$statsMin = self::$statsMax = array();
	}

	static public function parseStats($stats, $isMax = false)
	{
		if (!is_array($stats))
			$stats = explode(',', $stats);

		$html = '';
		foreach ($stats as $stat)
			$html .= self::parseStat($stat, $isMax) . '<br />';
		return $html;
	}

	static public function parseStat($stat, $isMax)
	{
		$stat = explode('#', $stat); #id#from#to#?#dice(XdY+Z)
		$rawType = strtolower($stat[0]);
		$factor = 1; //for reverse
		if (isset(self::$reverseTable[$rawType]))
		{
			$rawType = self::$reverseTable[$rawType];
			$factor = -1;
		}
		$type = lang($rawType, 'stat');

		if (!isset(self::$statsMin[$rawType]))
			self::$statsMin[$rawType] = 0;
		if (!isset(self::$statsMax[$rawType]))
			self::$statsMax[$rawType] = 0;
		$from = hexdec($stat[1]);
		$to = hexdec($stat[2]);
		if ($from > $to)
		{ //reverse, i.e. [xx]#1#0
			$from_ = $from;
			$from = $to;
			$to = $from_;
		}
		$from *= $factor; //multiply HERE because of the reverse check
		$to *= $factor;
		self::$statsMin[$rawType] += $from;
		self::$statsMax[$rawType] += $to;


		$from = self::statFromCode($from);
		$to = self::statFromCode($to);

		if ($to !== NULL && $from !== NULL && !$isMax)
			return sprintf(lang('stats'), $from, $to, $type);
		else
			return sprintf(lang('stats_simple'), $isMax || $from === NULL ? $to : $from, $type);
	}

	public static function statFromCode($code)
	{
		$code = intval($code);
		if ($code === 0)
			return NULL;
		else if ($code > 0)
			return '<b style="color: green;">+ ' . $code . '</b>';
		else
			return '<b style="color: red;">- ' . trim($code, '-') . '</b>';
	}

	public static function registerEffectsTooltip()
	{
		static $registered = false;
		if ($registered)
			return; //only 1 time
		$registered = true;

		jQ('$(".showEffects").tipTip();');
	}
}

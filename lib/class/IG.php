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
					),
					$breeds = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12),
					$genders = array(0, 1),
					$etheralJobs = array(44, 45, 46, 47, 48, 49, 50, 62, 63, 64),
					$etheralFor = array(15 => 62, 16 => 63, 27 => 64, 17 => 43, 11 => 44, 14 => 45, 20 => 46, 31 => 47, 13 => 48, 18 => 49, 19 => 50),
					$recoltJobs = array(2, 24, 26, 28, 36, 41),

					$expFloors = array(
						'job' => array(
							0,
							50,
							140,
							271,
							441,
							653,
							905,
							1199,
							1543,
							1911,
							2330,
							2792,
							3297,
							3840,
							4439,
							5078,
							5762,
							6493,
							7280,
							8097,
							8980,
							9898,
							10875,
							11903,
							12985,
							14122,
							15315,
							16564,
							17873,
							19242,
							20672,
							22166,
							23726,
							25353,
							27048,
							28815,
							30656,
							32572,
							34566,
							36641,
							38800,
							41044,
							43378,
							45804,
							48325,
							50946,
							53669,
							56498,
							59437,
							62491,
							65664,
							68960,
							72385,
							75943,
							79640,
							83482,
							87475,
							91624,
							95937,
							100421,
							105082,
							109930,
							114971,
							120215,
							125671,
							131348,
							137256,
							143407,
							149811,
							156481,
							163429,
							170669,
							178214,
							186080,
							194283,
							202839,
							211765,
							221082,
							230808,
							240964,
							251574,
							262660,
							274248,
							286364,
							299037,
							312297,
							326175,
							340705,
							355924,
							371870,
							388582,
							406106,
							424486,
							443772,
							464016,
							485274,
							507604,
							531071,
							555541,
							581687,
						),
					);

	static public function getLevel($exp, $type)
	{
		foreach (self::$expFloors[$type] as $i => $floor)
		{
			if ($exp < $floor)
				return $i; //O.K. since it starts at 0
		}
		return 100;
	}

	static public function getBreeds()
	{
		$breeds = array();
		foreach (self::$breeds as $id)
			$breeds[$id] = self::getBreed($id);
		return $breeds;
	}
	static public function getBreed($id)
	{
		return lang('breed.' . $id);
	}

	static public function getGenders()
	{
		$genders = array();
		foreach (self::$genders as $id)
			$genders[$id] = self::getGender($id);
		return $genders;
	}
	static public function getGender($id)
	{
		return lang('gender.' . $id);
	}

	static public function isEtheralJob($id)
	{
		return in_array($id, self::$etheralJobs);
	}
	static public function getEtheralJob($id)
	{
		return self::$etheralFor[$id];
	}
	static public function hasEtheralJob($id)
	{
		return isset(self::$etheralFor[$id]);
	}
	static public function isRecoltJob($id)
	{
		return in_array($id, self::$recoltJobs);
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
			$html .= ($s = self::parseStat($stat, $isMax)) == '' ? '' : $s . '<br />';
		return $html;
	}

	static public function parseStat($stat, $isMax)
	{
		$stat = explode('#', $stat); #id#from#to#?#dice(XdY+Z)

		if ($stat[1] == '0' && $stat[2] == '0')
			return '';

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

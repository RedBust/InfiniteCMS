<?php

/**
 * represents a couple of User + Account
 *
 * @file $Id: Member.php 53 2011-01-15 11:11:37Z nami.d0c.0 $
 */
class Member extends Multiton
{
	const CHAMP_LEVEL = 'level',
	CHAMP_PSEUDO = 'pseudo',
	CHAMP_PASS = 'pass';

	public function lang()
	{
		global $config, $account;
		if (!$this->isConnected() || empty($account->User->culture))
			return $config['use_lang'];
		return $account->User->culture;
	}

	public function pseudo($d = NULL)
	{
		return!empty($_POST[$this->CHAMP_PSEUDO]) ? $_POST[$this->CHAMP_PSEUDO] : $d;
	}

	public function level()
	{
		global $account;
		if (!empty($account) && $account instanceof Account)
			return $account->level;
		return LEVEL_GUEST;
	}

	public function init()
	{
		$this->lang = $this->lang();
	}

	/**
	 * @return boolean
	 */
	public function isSending()
	{
		return $this->pseudo() === NULL && $this->level() === NULL;
	}

	/**
	 * @return boolean
	 */
	public function isConnected()
	{
		return isset($_SESSION['guid']);
	}

	public function disconnect()
	{
		$_SESSION['guid'] = NULL;
		$_SESSION = array();
		session_destroy();
	}

	/**
	 * log
	 * logs the user
	 *
	 * @return User
	 */
	public function log($guid = NULL)
	{
		if (!isset($_SESSION['guid']))
			$_SESSION['guid'] = $guid;
		else if ($guid === NULL)
			$guid = $_SESSION['guid'];

		return UserTable::fromGuid($guid);
	}
	/*
	 * Retourne du texte représentatif du rang
	 *
	 * @param integer $lvl Niveau du membre
	 */

	public static function formateLevel($lvl)
	{
		$lvl = intval($lvl);
		$lvls = self::getFormattedLevels();
		if ($lvl > LEVEL_ADMIN)
			$lvl = LEVEL_ADMIN;
		return $lvls[$lvl];
	}

	public static function getLevels()
	{
		return array(
			LEVEL_GUEST => lang('rank.guest'),
			LEVEL_LOGGED => lang('rank.player'),
			LEVEL_TEST => lang('rank.test'),
			LEVEL_MODO => lang('rank.mod'),
			LEVEL_MJ => lang('rank.gm'),
			LEVEL_ADMIN => lang('rank.admin'),
		);
	}

	public static function getFormattedLevels()
	{
		return array(
			LEVEL_GUEST => lang('rank.guest'),
			LEVEL_LOGGED => '<i>' . lang('rank.player') . '</i>',
			LEVEL_TEST => '<u>' . lang('rank.test') . '</u>',
			LEVEL_MODO => '<b><i>' . lang('rank.mod') . '</i></b>',
			LEVEL_MJ => '<b><u>' . lang('rank.gm') . '</u></b>',
			LEVEL_ADMIN => '<b><i><u>' . lang('rank.admin') . '</u></i></b>',
		);
	}

	public static function getIp()
	{
		//for the following code: yeah, it's "isset _server.remote_addr", and not http_x ... prevent tricks
#		return isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] :
#		 $_SERVER['HTTP_X_REQUESTED_FOR'];
		return $_SERVER['REMOTE_ADDR'];
	}
}
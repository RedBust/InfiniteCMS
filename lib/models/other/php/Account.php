<?php

/**
 * Account
 * 
 * @package    InfiniteCMS
 * @subpackage Models
 * @author     Nami-Doc <nami.d0c.0@gmail.com>
 * @version    SVN: $Id: Account.php 56 2011-01-16 19:39:32Z nami.d0c.0 $
 */
class Account extends BaseAccount
{
	public static $preSend = false;
	private $profilSent = false;
	public static $profils = array();
	public $charsInit = false;
	private $amis = NULL,
			$rFriends = NULL; //reverse friends 
	protected $isEncrypted = false;

	public static function sendPreProfil()
	{
		if (self::$preSend)
			return;
		self::$preSend = true;

		jQ('
var profils = {},
	profilBox = $("#profil");
profilBox.dialog($.extend(dialogOpt, {"modal": false}));
function showProfil(id)
{
	profilBox
		.find("div")
		.hide();
	profilBox
		.find("#profil-" + id)
		.show();
	profilBox.dialog("open");
}
pageBind(function (undef)
{
	delete profils;
	if (profilBox != undef)
	{
		profilBox.dialog("close");
		delete profilBox;
	}
	delete showProfil;
});');
		echo tag('div', array(
			'id' => 'profil',
			'style' => 'display: none;',
			'title' => sprintf(lang('infos')),
		), '');
	}

	public function sendProfil()
	{
		if ($this->profilSent)
			return;
		global $account, $router, $cache;
		$this->profilSent = true;
		self::sendPreProfil();

		$accountId = $this->guid;
		if (!in_array($accountId, self::$profils))
		{
			self::$profils[] = $accountId;
			global $member, $connected, $router;
			//todo => global $acc = $this;
			if ($profil_cache = Cache::start('Account_show_' . $this->guid . '_' . ($connected ? $account->guid : -1) . '_profile', strtotime('+3 hours')))
			{
				echo tag('div', array(
					'id' => 'profil-' . $this->guid,
					'style' => 'display: none;',
				), require $router->getPath('Account', 'show'));
				$profil_cache->save(Cache::SHOW, Cache::NO_JS);
			}
			if ($cache instanceof Cache)
					$cache->put('<?php global $router, $connected, $account;
if($c = Cache::start("Account_show_' . $this->guid . '_" . ($connected ? $account->guid : -1) . "_profile", strtotime("+3 hours")))
{
	global $accountId;
	$accountId = ' . $this->guid . ';
	echo tag("div", array(
			"id" => "profil-' . $this->guid . '",
			"style" => "display: none;",
		), require $router->getPath("Account", "show"));
	$c->save(Cache::SHOW, Cache::NO_JS);
}
?>');
			jQ('profilBox.append($("#profil-' . $this->guid . '"))'); //jQuery.fn.append
		}
	}

	public function getLink($text = NULL)
	{
		$this->sendProfil();
		return js_link('showProfil(' . $this->guid . ');', $text === NULL ? $this->getName() : $text, replace_url($this, true, array()));
	}

	public function getName()
	{
		return $this->banned ? tag('strike', $this->pseudo) : $this->pseudo;
	}

	/**
	 * @return float number representation of the level
	 */
	public function getLvl()
	{
		return Member::adjustLevel($this->level, $this->vip);
	}
	/**
	 * @return string string representation of the level (html mix)
	 */
	public function getFormattedLevel()
	{
		return Member::formateLevel($this->level, $this->vip);
	}
	/**
	 * @return string html representation of the level (Edit In Place mix)
	 */
	public function getLevel($format = true)
	{
		return tag('span', array(
			'class' => 'f_level',
			'data-id' => $this->guid,
		), $format ? $this->getFormattedLevel() : $this->getLvl());
	}
	public function isVIP()
	{
		global $config;
		if (level(LEVEL_ADMIN))
			return true;
		return empty($config['COST_VIP']) ? false : $this->vip;
	}

	public function canJudge($contest)
	{
		if ($contest->ended || !$this->isJury($contest))
			return false;

		return !$contest->Voters->contains($this->getUser()->id);
	}
	public function isJury($contest)
	{
		if ($this->level >= $contest->level)
			return true;

		return $contest->Jurors->contains($this->getUser()->id);
	}
	public function canCompete($contest)
	{
		if ($contest->ended || !$this->getMainChar())
			return false;

		return !$this->currentlyCompete($contest);
	}
	public function currentlyCompete($contest)
	{
		if ($contest instanceof Contest || is_array($contest))
			$contest = $contest['id'];


		foreach ($this->Characters as $character)
		{
			if ($character->ContestParticipations->contains($contest))
				return true;
		}
		return false;
	}
	public function canParticipate($event)
	{
		if (!$this->getMainChar())
			return false;

		return !$this->currentlyParticipate($event);
	}
	public function currentlyParticipate($event)
	{
		if ($event instanceof Event || is_array($event))
			$event = $event['id'];

		foreach ($this->Characters as $character)
		{
			if ($character->Events->contains($event))
				return true;
		}
		return false;
	}
	public function canSetWinner(Event $e)
	{
		if ($this->level >= LEVEL_ADMIN)
			return true;
		if (!$e->relatedExists('Guild'))
			return false;

		if (!$mainChar = $this->getMainChar())
			return false;
		return $mainChar->isGM($e->guild_id);
	}

	/**
	 * returns characters list as string, to select something
	 *
	 * @param bool $accordion format for accordion (h3 + p) ?
	 * @param array $exclude characters ID to exclude from being SELECTED (still shown, may modify this behavior later)
	 * @param string $fn the JS function to call. You must include the 1st parenthesis (that allows to call, i.e. ...(.., .., 'doWTFYaWant(1, 3, ')
	 * @param mixed $normalLink link for replacement in js_link
	 *
	 * @return string html (jq-ui-accordeon'able if $accordion) list
	 */
	public function getCharactersList($accordion = false, $exclude = array(), $fn = 'choosePerso(', $normalLink = array())
	{
		if (empty($exclude))
			$exclude = array();
		else if (!is_array($exclude))
			$exclude = array($exclude);

		if (is_array($fn) && empty($normalLink))
		{
			$normalLink = $fn;
			$fn = NULL;
		}

		if ($this->Characters->count())
		{
			$persos = '';
			foreach ($this->Characters as $character)
			{ //@todo refactor to allow molet-clic
				$persos .= $character->toString(true, $accordion) .
				 (in_array($character->guid, $exclude) ? '' : js_link($fn . $character->guid . ')', lang('choose'), to_url($normalLink).$character->guid)) .
				 ($accordion ? "</p></div>\n" : tag('br') . tag('br'));
			}
			return $persos;
		}
		return '';
	}

	/**
	 * returns friends
	 *
	 * @return Collection list of friends
	 */
	public function getFriends()
	{
		if (empty($this->friends) || $this->friends == ';')
			return array();

		if ($this->amis === NULL)
		{ //init
			$this->amis = $this->getTable()
								->createQuery()
									->whereIn('guid', explode(';', $this->friends))
									->andWhere('guid != ?', $this->guid)
								->execute();
		}
		return $this->amis;
	}
	/**
	 * @return Collection list of reverse friends
	 */
	public function getReverseFriends($hydrate = null)
	{
		if ($this->rFriends === NULL)
		{
			$this->rFriends = new Collection(__CLASS__);
			foreach ($this->getFriends() as $friend)
			{
				if (in_array($this->guid, explode(';', $friend->friends)))
					$this->rFriends[] = $friend;
			}
		}
		return $this->rFriends;
	}

	/**
	 * determines if this Account has this friend
	 *
	 * @param Account $acc The account that may be the friend
	 * @return boolean true if this account has this friend, otherwise false
	 */
	public function hasFriend(Account $acc)
	{
		if ($acc->guid == $this->guid)
			return false; //NO you can't have yourself as a friend§ umadbro?
		foreach ($this->getFriends() as $friend)
		{ /* @var $friend Account */
			if ($acc->guid == $friend->guid)
				return true;
		}
		return false;
	}
	public function hasReverseFriend(Account $acc)
	{
		if ($acc->guid == $this->guid)
			return false;
		foreach ($this->getReverseFriends as $rFriend)
		{
			if ($acc->guid == $rFriend)
				return true;
		}
		return false;
	}

	/**
	 * executes action before insert
	 *
	 * @package Doctrine
	 * @subpackage hooks
	 *
	 * @access public
	 * @param Doctrine_Event $event Evenement déclencheur
	 * @return void
	 */
	public function preInsert(Doctrine_Event $event)
	{
		global $member, $config;

		$inv = $event->getInvoker();
		// @FIXME add some checks from proxy'n'll
		$inv->lastip = ip2long($member->getIp());
		$inv->lastconnectiondate = new Doctrine_Expression('NOW()');
	}
	public function preSave(Doctrine_Event $event)
	{
		Cache::destroyPrefix('Account_show_' . $this->guid);
	}

	public function getColumns()
	{
		$columns = array('pass', 'pseudo', 'question', 'reponse');
		if (!$this->exists() || level(LEVEL_ADMIN))
		{
			array_unshift($columns, 'account');
			$columns[] = 'email';
		}
		if (level(LEVEL_ADMIN))
			$columns[] = 'level';

		return $columns;
	}

	public function setLevel($level)
	{
		global $config;

		$level = floatval($level);
		if ($level == LEVEL_BANNED) //just put "banned", don't modify the level. But ... "LEVEL_BANNED" is not in the ranks list !
		{
			$this->banned = 1;
			$level = LEVEL_LOGGED;
		}
		else if ($this->banned)
			$this->banned = 0;

		if ($level == LEVEL_VIP)
		{
			if (empty($config['COST_VIP']))
				$level = LEVEL_LOGGED;
			else
			{
				$this->vip = 1; //@todo reset level to LOGGED ?
				return;
			}
		}
		else if ($level == LEVEL_LOGGED && $this->vip)
			$this->vip = 0; //no continue.
		else if ($level <= LEVEL_GUEST)
			$level = LEVEL_LOGGED;
		else if ($level > LEVEL_ADMIN)
			$level = LEVEL_ADMIN;

		$this->_set('level', $level);
	}

	public function setUp()
	{
		parent::setUp();
		$this->hasMutator('level', 'setLevel');
	}
	/**
	 * updates the account
	 *
	 * @global Accounts $account
	 *
	 * @param array $values Values
	 * @param array $col -default=NULL Columns
	 * @param boolean $new -default=true New record ?
	 * @return array Errors
	 */
	public function update_attributes(array $values, $columns = NULL)
	{
		global $account, $member, $config;
		$errors = array();
		if (isset($values['email']))
			$values['email'] = strtolower($values['email']);

		if (empty($columns) || $columns === true)
		{ 
			$columns = $this->getColumns(); //no, no, it's not getTable()->getColumnNames
		}
		if (is_string($columns))
			$columns = explode(';', $columns);
		if (level(LEVEL_ADMIN))
			$values['banned'] = isset($values['banned']);

		if (!empty($values['email']))
			$values['email'] = strtolower($values['email']);

		foreach ($columns as $t)
		{
			$t[0] = is_string($t[0]) ? strtolower($t[0]) : intval($t[0]);
			if (!isset($values[$t]))
			{
				if ($t === 'banned')  //WTF????
					$this->banned = 0;
				else
					$errors[] = sprintf(lang('must_!empty'), $t);
			}
			else
			{
				$this->$t = $values[$t];
			}
		}
		$this->lastip = ip2long($member->getIp());
		if (!filter_var($this->email, FILTER_VALIDATE_EMAIL))
		{
			$errors['email'] = lang('acc.register.error.mail');
		}
		if (( !isset($values['tos']) || ( $values['tos'] !== 'on' && $values['tos'] !== '' ) )
		 && !$this->exists())
		{
			$errors['tos'] = sprintf(lang('acc.register.error.tos'), make_link('@tos', lang('cgu')));
		}
		$check = Query::create()
				->from(__CLASS__) //why do I select the account instead of a count() ? Because I need infos ...
				->where('(LOWER(account) = ? OR LOWER(pseudo) = ? OR LOWER(email) = ? or lastip = ?)', array(
					strtolower($this->account),
					strtolower($this->pseudo),
					$this->email,
					$this->lastip,
				));
		if ($this->exists())
			$check->andWhere('guid != ?', $this->guid);
		$check = $check->fetchOne();
		if ($check)
		{
			if ($this->email == $check->email)
			{
				$errors['email'] = lang('acc.register.error.mail_used');
			}
			if ($this->account == $check->account)
			{
				$errors['account'] = lang('acc.register.error.account_used');
			}
			if ($this->pseudo == $check->pseudo)
			{
				$errors['pseudo'] = lang('acc.register.error.pseudo_used');
			}
			if ($check->banned
			 || ($this->lastip == $check->lastip && !$config['ALLOW_MULTI']))
			{
				$errors['_'] = lang('acc.register.error.already_created_acc');
			}
		}
		if (strpos($this->pseudo, $this->account) !== false
		 || strpos($this->account, $this->pseudo) !== false
		 || strpos($this->pass, $this->account) !== false
		 || strpos($this->account, $this->pass) !== false
		 || strpos($this->pass, $this->pseudo) !== false
		 || strpos($this->pseudo, $this->pass) !== false
		 || strpos($this->question, $this->pseudo) !== false
		 || strpos($this->question, $this->account) !== false
		 || strpos($this->question, $this->pass) !== false
		 || strpos($this->reponse, $this->pseudo) !== false
		 || strpos($this->reponse, $this->account) !== false
		 || strpos($this->reponse, $this->pass) !== false)
		{ //:'). In fact : if [pseudo, account, pass].any in([pseudo, account, pass, question, reponse]) then fail
			$errors['unsafe_dup'] = lang('acc.register.unsafe_dup'); //duplication
		}
		if ($errors === array())
		{
			$this->save();
		}

		return $errors;
	}

	public function getUser()
	{
		if (!$this->relatedExists('User'))
			$this->User = UserTable::getInstance()->fromGuid($this);
		return $this->User;
	}
	public function getMainChar()
	{
		global $account;

		if (!$this->Characters->count())
			return null;
		if ($this->getUser()->main_char != 0 && $this->Characters->contains($this->getUser()->main_char))
			return $this->Characters[$this->getUser()->main_char];

		if ($account instanceof Account && $account->guid == $this->guid)
			jQ('$("#firstMainChar").dialog(dialogOptO);');


		$main_char = $this->Characters->getFirst();
		if ($this->Characters->count() != 1)
		{
			foreach ($this->Characters as $character)
			{
				if ($main_char instanceof Character && $character->xp > $main_char->xp)
					$main_char = $character;
			}
		}
		$this->getUser()->main_char = $main_char->guid;

		if ($account instanceof Account && $account->guid != $this->guid)
			$this->getUser()->save();

		return $main_char;
	}

	public function getRolesString()
	{
		$isAdmin = level(LEVEL_ADMIN);

		$html = '<ul>';
		foreach ($this->StaffRoles as $role)
			$html .= tag('li', $role->getName() . ($isAdmin ? $role->getUpdateLink() . $role->getDeleteLink() : ''));
		if ($isAdmin)
			$html .= tag('li', $this->getNewStaffRoleLink());

		return $html . '</ul>';
	}

	public function getNewStaffRoleLink()
	{
		return make_link(array('controller' => 'StaffRole', 'action' => 'update', 'account' => $this->guid), lang('StaffRole - create', 'title'));
	}
}

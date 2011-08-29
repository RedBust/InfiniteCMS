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
	public $amis = array();
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
binds.add(function (undef)
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
			if ($profil_cache = Cache::start('Account_show_profil_' . $this->guid . '_' . ($connected ? $account->guid : -1), strtotime('+3 hours')))
			{
				$cont = require $router->getPath('Account', 'show');
				echo tag('div', array(
					'id' => 'profil-' . $this->guid,
					'style' => 'display: none;',
				), $cont);
				$profil_cache->save(Cache::SHOW, Cache::NO_JS);
			}
			if ($cache instanceof Cache)
					$cache->put('<?php global $router, $connected, $account;
if($c = Cache::start("Account_show_profil_' . $this->guid . '_" . ($connected ? $account->guid : -1), strtotime("+3 hours")))
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
		return js_link('showProfil(' . $this->guid . ');', $text === NULL ? $this->getName() : $text, to_url(array(
			'controller' => 'Account',
			'action' => 'show',
			'id' => $this->guid
		)));
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
		if (level(LEVEL_TEST)) //in-test mod (reminder)
			return true;
		return $this->vip;
	}

	public function canParticipate($event)
	{
		if (!$this->Characters->count())
			return false;

		if ($event instanceof Event || is_array($event))
			$event = $event['id'];

		foreach ($this->Characters as $character)
		{
			if ($character->Events->contains($event))
				return false;
		}
		return true;
	}

	public function getCharactersList($accordion = false)
	{
		if ($this->Characters->count())
		{
			$persos = '';
			foreach ($this->Characters as $character)
			{ //@todo refactor to allow molet-clic
				$persos .= $character->toString(true, $accordion) . js_link('choosePerso(' . $character->guid . ' )', lang('choose')) .
				 ($accordion ? "</p></div>\n" : tag('br') . tag('br'));
			}
			return $persos;
		}
		return '';
	}

	/**
	 * return friends
	 *
	 * @return Collection list of friends
	 */
	public function getFriends()
	{
		if (empty($this->friends))
			return array();

		if ($this->amis === array())
		{ //init
			$this->amis = Query::create()
					->from(__CLASS__)
						->whereIn('guid', explode(',', $this->friends))
					->execute();
		}
		return $this->amis;
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

	public function getColumns()
	{
		$columns = array('pass', 'pseudo', 'question', 'reponse');
		if (!$this->exists() || level(LEVEL_ADMIN))
		{
			array_unshift($columns, 'account');
			$columns[] = 'email';
		}
		if (level(LEVEL_ADMIN))
			$columns = array_merge($columns, array('banned', 'level'));

		return $columns;
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

		if (empty($columns) || $columns === true)
		{
			//no, no, it's not getTable()->getColumnNames
			$columns = $this->getColumns();
		}
		if (level(LEVEL_ADMIN))
			$values['banned'] = isset($values['banned']);
		if (is_string($columns))
		{
			$columns = explode(';', $columns);
		}
		foreach ($columns as $t)
		{
			$t[0] = is_string($t[0]) ? strtolower($t[0]) : intval($t[0]);
			if (!isset($values[$t]))
			{
				if ($t === 'banned')
					$this->banned = 0;
				else
					$errors[] = sprintf(lang('must_!empty'), $t);
			}
			else
			{
				if ($t === 'level')
				{
					if ($values[$t] == LEVEL_BANNED)
					{ //just put "banned", don't modify the level. But ... "LEVEL_BANNED" is not in the ranks list !
						$this->banned = 1;
						continue;
					}
					if ($values[$t] == LEVEL_VIP)
					{
						$this->vip = 1;
						continue; //reset level to LOGGED? 
					}
					if ($values[$t] == LEVEL_GUEST)
						$values[$t] = LEVEL_LOGGED; //would delete the account ... and it'd be stupid.
					if ($values[$t] == LEVEL_LOGGED && $this->vip)
					{
						$this->vip = 0; //no continue.
					}
					if ($values[$t] > LEVEL_ADMIN)
						$values[$t] = LEVEL_ADMIN;
				}
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
				->where('(account = ? OR pseudo = ? OR email = ? or lastip = ?)', array(
					$this->account,
					$this->pseudo,
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
		{ //:'). In fact : if [pseudo, account, pass].any in [pseudo, account, pass, question, reponse] then fail
			$errors['unsafe_dup'] = lang('acc.register.unsafe_dup'); //duplication
		}
		if ($errors === array())
		{
			$this->save();
		}

		return $errors;
	}

	public function getId()
	{
		return $this->guid;
	}
	public function getMainChar()
	{
		if (!$this->Characters->count())
			return null;
		if (!$this->relatedExists('User'))
			$this->User = UserTable::getInstance()->fromGuid($this);
		if ($this->User->main_char != 0 && $this->Characters->contains($this->User->main_char))
			return $this->Characters[$this->User->main_char];

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
		$this->User->main_char = $main_char->guid;
		return $main_char;
	}
}

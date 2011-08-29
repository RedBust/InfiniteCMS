<?php

/**
 * adds more possibility to the basic Doctrine_Collection class
 *
 * @file $Id: Collection.php 40 2010-11-21 01:15:23Z nami.d0c.0 $
 *
 * @extends Doctrine_Collection
 */
class Collection extends Doctrine_Collection
{

	//static in case we have many collection of Character ...
	protected static $charsInit = NULL;

	/**
	 * adds all values to this Collection
	 *
	 * @param $values Collection|array Values to add
	 * @return void
	 */
	public function addAll($values)
	{
		foreach ($values as $id => $rec)
		/** @var $rec Record */
			$this->add($rec);
	}
	/**
	 * creates an array of each $record[$key]
	 *
	 * @return array [records[0].key, records[1].key, ...]
	 */
	public function getKeyArray($key)
	{
		$records = array();
		foreach ($this as $record)
			$records[] = $record[$key];
		return $records;
	}

	/**
	 * isEmpty
	 * determinate if this Collection contains or not records
	 *
	 * @return boolean is this Collection is empty
	 */
	public function isEmpty()
	{
		return $this->count() === 0;
	}

	/**
	 * getCleanValues
	 * keep values
	 *
	 * @return array
	 */
	public function getCleanValues()
	{
		$datas = $this->toArray();
		if (!empty($datas) && $this->getTable()->hasColumn('content'))
			foreach ($datas as &$data)
				$data['content'] = News::format($data['content']);
		return $datas;
	}

	/**
	 * ladderDisplay
	 * display this Collection as a ladder
	 *
	 * @param int $startAt start position
	 * @param String $char sought character's name
	 * @return boolean Is this collection contains key ? (isEmpty)
	 */
	public function ladderDisplay($startAt = 0, $char = '')
	{
		if ($this->isEmpty())
			return false;
		$td = '<td valign="center" align="center">';
		echo '
			<table border="1" style="width: 95%">',
		tag('tr', $td . tag('b', lang('acc.ladder.pos')) . '</td>' .
				$td . tag('b', lang('character')) . '</td>' .
				$td . tag('b', lang('level')) . '</td>' .
				$td . tag('b', lang('acc.ladder.guild')) . '</td>' .
				$td . tag('b', lang('acc.ladder.class')) . '</td>' .
				( level(LEVEL_MJ) ?
						$td . tag('b', lang('pseudo')) : '' ));
		$td = '<td valign="center">';
		foreach ($this as $perso)
		{
			/* @var $perso Character */
			if ($perso->relatedExists('GuildMember') && $perso->GuildMember->rank !== NULL)
				$g = make_link($perso->GuildMember->Guild);
			else
				$g = tag('i', lang('acc.no_guild'));
			$align = array('align' => 'center');
			if ($char == $perso['name'])
				$opt = array('id' => 'selected-char');
			else
				$opt = array();
			echo tag('tr', $opt, tag('td', $align, ++$startAt) .
					tag('td', $align, make_link($perso)) .
					tag('td', $align, $perso['level']) .
					tag('td', $align, $g) .
					tag('td', $align, make_img('classes/' . strtolower(substr($perso->getBreed(), 0, 3)) . '_' . $perso['sexe'], EXT_PNG)) .
					( level(LEVEL_MJ) ? tag('td', $align, make_link($perso->Account)) : ''));
		}
		echo '
		</table>';
		return true;
	}

	public function shopDisplay()
	{
		global $config, $types, $account, $router;
		$html = '';
		$table = ShopItemTable::getInstance();

		if ($this->isEmpty())
		{
			echo tag('h3', lang('shop.item.no_by_criteria'));
			return;
		}
		$itemsID = array(); //BIG array of alls items ID
		foreach ($this as $obj)
		{
			foreach ($obj->Effects as $ef)
				if ($ef->isItem())
					$itemsID[] = $ef->value;
		}
		$items = Query::create()
				->from('ItemTemplate it INDEXBY id')
					->whereIn('id', $itemsID)
				->execute();

		$count = $this->count();
		$i = 0;
		$html .= '
	<table border="1">
		<tr>';
		define('FROM_INCLUDE', true);
		if (level(LEVEL_ADMIN))
		{
			$type_opt = array();
			foreach ($types as $val => $text)
			{
				$type_opt[] = '"' . $val . '": "' . $text . '"';
			}

			$url = array(
				'controller' => 'Shop',
				'action' => 'update',
				'output' => 0,
				'id' => '%%t.data("id")%%',
				'col' => '',
			);
			jQ('
field_opts = {' . implode("\n" . ', ', $type_opt) . '};
binds.add(function ()
{
	delete field_opts;
});');
			foreach (array('name', 'cost', 'cost_vip') as $t)
				jQ('
$(".f_' . $t . '").each( function ()
{
	var t = $(this);
	t.editInPlace(
	{
		url: "' . to_url($url, false) . $t . '",
	} );
} );');
		}
		foreach ($this as $item)
		{
			$m = $i++ % $config['ITEMS_BY_LINE'];
			if ($m === 0 && $i !== 1) //start a new line
				$html .= '
		</tr>
		<tr>';
			$html .= tag('td', $i === $count ? array('colspan' => strval($config['ITEMS_BY_LINE'] - $m)) : array(), $item);
		}
		$html .= '
			</tr>
	</table>';
		echo $html;
		IG::registerEffectsTooltip();
	}

	public function atomDisplay($title = NULL, $desc = NULL, $link = NULL)
	{
		global $router;

		if ($title === NULL)
			$title = 'RSS';
		if ($desc === NULL)
			$desc = $router->getControllerUse($router->getController()) . ' &bull; RSS';
		if ($link === NULL)
			$link = to_url(array(
						'controller' => $router->getControllerUse($router->getController()),
						'action' => 'show',
						'id' => ''
					)) . '{id}';
		echo '<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="http://rss.feedsportal.com/xsl/fr/rss.xsl"?>
<rss
	xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/"
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" version="2.0">
	<channel>
		<title>' . $title . '</title>
		<link>' . str_replace('index' . EXT, '', $_SERVER['PHP_SELF']) . '</link>
		<description>' . $desc . '</description>';
		$table = $this->getTable();
		/* @var $table Table */
		$hasColumn = array(
			'title' => $table->hasColumn('title'),
			'content' => $table->hasColumn('content'),
			'desc' => $table->hasColumn('description'),
			'date' => $table->hasColumn('created_at'),
		);
		foreach ($this->getCleanValues() as $val)
		{
			//$pre & $post are used > 1 times
			$pre = "\n\t\t\t";
			$post = "\n\t\t";
			$title = $hasColumn['title'] ? $pre . tag('title', $val['title']) : '';
			$desc = $hasColumn['desc'] ? $val['description'] : ( $hasColumn['content'] ? $val['content'] : '' );
			$desc = $pre . tag('description', sprintf('<![CDATA[%s]]>', $desc));
			$date = $hasColumn['date'] ? $pre . tag('pubDate', $val['created_at']) : '';
			$link = str_replace('{id}', $val['id'], $link);
			echo $post . tag('item', $title . $pre . tag('link', $link) . $desc . $date . $post);
		}
		echo '
	</channel>
</rss>';
	}

	public function jsonDisplay()
	{
		echo json_encode($this->getCleanValues());
	}

	public function charactersDisplay($url)
	{
		if ($this->isEmpty())
			return;
		global $router;
		$persos = $normal = '';
		$modeNormal = $router->requestVar('mode') != 'adv';
		foreach ($this as $perso)
		{ /* @var $perso Character */
			$persos .= $perso->asTableRow();
			$normal .= tag('li', tag('b', '&bull;&nbsp;' . make_link($perso)));
		}
		$less = $normalParams = $url;
		$more = $normalParams + array(//show more (used only if JS is disabled)
			'mode' => 'adv',
		);
		$toggle = 'jQuery( "#%s" ).slideUp(); jQuery( "#%s" ).slideDown();';
		echo tag('div', array(
			'id' => 'normal',
			'style' => 'display: ' . ( $modeNormal ? 'block' : 'none' ) . ';',
				), tag('ul', $normal), js_link(sprintf($toggle, 'normal', 'adv'), lang('more'), to_url($more))),
		tag('div', array(
			'id' => 'adv',
			'style' => 'display: ' . ( $modeNormal ? 'none' : 'block' ) . ';'
		), tag('table', array(
			'border' => 1,
			'style' => 'width: 100%;',
		), $this[0]->getTable()->getTableHeader() . $persos) . js_link(sprintf($toggle, 'adv', 'normal'), lang('less'), to_url($less)));
	}

	public function init()
	{
		if ($this->isEmpty())
			return;
		switch (get_class($this[0]))
		{
			case 'Character':
				$this->_charsLoad();
				break;
		}
	}

	public static function charLoad($char)
	{
		self::_charsInit();
		if (!in_array($char->guid, self::$charsInit))
		{
			self::$charsInit[] = $char->guid;
			jQ('cProfils[' . $char->guid . '] = \'' . $char->getInfoBox() . '\';');
		}
	}

	public static function _charsInit()
	{
		global $config;
		if (self::$charsInit === NULL)
		{
			self::$charsInit = array();
			$js = '';
			echo tag('div', array(
				'id' => 'c_profil',
				'style' => 'display: none;',
				'title' => lang('infos'),
					), '');
			jQ('
var cProfils = {},
	cProfilBox = $( "#c_profil" );
' . ( $config['LOAD_TYPE'] == LOAD_NONE ? '/*' : '' ) . '$( "#closeProfilBox" ).live( "click", function (event)
	{
		cProfilBox.dialog( "close" );
		event.preventDefault();
	} ); /**/
cProfilBox.dialog(
	{
		autoOpen: false,
		draggable: true,
		modal: true,
		resizable: true,
		width: 600,
	} );
function showChar(id)
{
	cProfilBox.dialog( "open" ).html( cProfils[id] );
}');
		}
	}

	protected function _charsLoad()
	{
		foreach ($this as $perso)
		{
			/* @var $perso Character */
			self::charLoad($perso);
		}
	}
}

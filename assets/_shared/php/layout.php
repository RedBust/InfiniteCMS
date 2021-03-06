<!DOCTYPE html>
<html>
	<head>
		<title>
			<?php echo $title ?>

		</title>
		<?php
			//@todo prod.css & prod.js
			stylesheet_tag('style');
			if ($config['JAVASCRIPT'])
				stylesheet_tag('jQuery.UI', 'jQuery.tipTip', 'jQuery.tokenInput.Facebook', 'jQuery.tokenInput');

			echo stylesheet_tag();
		?>
		<style type="text/css">
		.showThis
		{
			display: none;
		}
		.myChar a
		{
			color: red;
		}
		#right #servInfo
		{
			height:75px;
			background:url(<?php echo asset_path('status' . SERVER_STATE . EXT_JPG, ASSET_IMAGE) ?>) no-repeat;
			width: 184px;
			margin-left: 22px;
		}
		</style><?php
		foreach ($metas as $equiv => $content)
		{
			echo tag('meta', array('http-equiv' => $equiv, 'content' => $content));
		}
		?>
	</head>
	<body>
		<?php
		//jQuery UI box (loading & error)
		echo tag('div', array(
			'id' => 'loading',
			'style' => 'display: none;',
			'title' => lang('loading'),
			'align' => 'center'
		), "\n\t\t\t" . tag('br') . tag('b', lang('loading') . ' ...') . "\n\t\t"),
		 "\n\t\t", tag('div', array(
			'id' => 'errorDiv',
			'style' => 'display: none;',
			'title' => lang('error'),
			'align' => 'center', //@todo => marge
		), "\n\t\t\t" . tag('br') . tag('b', array('id' => 'error'), '&nbsp;') . "\n\t\t");
		if (level(LEVEL_LOGGED))
		{
			echo tag('div', array(
				'id' => 'firstMainChar',
				'style' => 'display: none;',
				'title' => lang('character.main'),
			), lang('character.main.new')), tag('div', array(
				'id' => 'selectMainChar',
				'style' => 'display: none;',
				'title' => lang('character.main'),
			), tag('div', array('id' => 'charactersList'), $account->getCharactersList(true, $account->getMainChar() ? $account->getMainChar()->guid : array(), 'chooseMainChar('))),
			tag('div', array(
				'id' => 'pm',
				'style' => 'display: none;',
				'title' => lang('PrivateMessage - index', 'title'),
			), '');
		}
		?>

		<div id="contenteur"><!-- Template and div IDs are from Woa, not me :p -->
		<?php
		echo tag('div', array('id' => 'header'),
		 tag('div', array('class' => 'play'),
		  make_link('@join', make_img('playr', EXT_PNG)))) //not so clean :/
		?>

			<div id="contenu">
				<div id="left">
					<?php partial('left', PARTIAL_FULL) ?>
				</div>
				<div id="milieu">
					<?php echo $data /* $data contains the html returned by the action */ ?>
				</div>
				<div id="right">
					<?php partial('right', PARTIAL_FULL) ?>
				</div>
			</div>
			<?php partial('footer', PARTIAL_FULL) ?>
			<!--div id="footer">
				THIS IS A FOOTER, SO RIGHT SO GOOD ISN'T IT (OK/AC, actually commented. Don't you know how to remove <!-- and -- > ?!
				<!--InfiniteCMS <?php echo VERSION ?> by Nami-Doc. 2009-<?php echo date('Y') ?> -- >
			</div-->
			<?php $config['JAVASCRIPT'] && partial('js', PARTIAL_FULL) ?>
		</div>
	</body>
</html>

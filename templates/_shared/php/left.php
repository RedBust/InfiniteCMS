<?php
//this file is out of layout because you may edit it lots more than you
// may wanna edit the global layout. This avoids any mistake.
//Ce fichier est en-dehors du layout pour vous permettre de modifier le menu en étant sûr de ne pas toucher au reste
$prefix = '~ ';
if ($config['URL_VOTE'] != -1):
?>
<div class="module1">
	<?php echo make_link('@vote', make_img('votez', EXT_JPG), array(), array(), false) ?>
</div>
<?php endif ?>
<div class="module2">
	<div class="title slideMenu" style="margin-left: 5px;"><?php echo lang('part.home') ?></div>
	<ul>
		<li>
			<?php echo make_link('@root', $prefix . lang('News - index', 'title')) ?>
		</li>
		<li>
			<?php echo make_link('@join', $prefix . lang('Account - join', 'title')) ?>
		<li>
		<?php if (!$connected && $config['ENABLE_REG']): ?>
		<li>
			<?php echo make_link(array('controller' => 'Account', 'action' => 'create'), $prefix . lang('Account - create', 'title')) ?>
		</li>
		<?php endif ?>
		<li>
			<?php echo make_link('@staff', $prefix . lang('StaffRole - index', 'title')) ?>
		</li>
		<li>
			<?php echo make_link('@tos', $prefix . lang('menu.rules')) ?>
		</li>
	</ul>
</div>
<br /><br />
<div class="module4">
	<div class="title slideMenu" style="margin-left: 5px;"><?php echo lang('part.server') ?></div>
	<ul>
		<li>
			<?php echo make_link('@cgu_serv', $prefix . lang('menu.rules')) ?>
		</li>
		<?php if ($config['TEAMSPEAK']['ENABLE']): ?>
		<li>
			<?php echo make_link(array('controller' => 'Misc', 'action' => 'ts'), $prefix . lang('Misc - ts', 'title')) ?>
		</li>
		<?php endif ?>
		<li>
			<?php echo make_link(array('controller' => 'Misc', 'action' => 'server'), $prefix . sprintf(lang('menu.infos_comp'), $config['SERVER_NAME'])) ?>
		</li>
	</ul>
</div>
<div class="module4">
	<div class="title slideMenu" style="margin-left: 5px;"><?php echo lang('part.interactif') /*this relies on DB info*/ ?></div>
	<ul>
		<li>
			<?php echo make_link('@ladder', $prefix . lang('ladder.PvM')) ?>
		</li>
		<?php if ($config['URL_VOTE'] != -1): ?>
		<li>
			<?php echo make_link('@ladder_vote', $prefix . lang('ladder.vote')) ?>
		</li>
		<?php
		endif;
		if ($config['ENABLE_SHOP']):
		?>
		<li>
			<?php echo make_link('@shop', $prefix . lang('shop.title')) ?>
		</li>
		<?php endif ?>
		<li>
			<?php echo make_link('@events', $prefix . lang('Event - index', 'title')) ?>
		</li>
		<li>
			<?php echo make_link('@contests', $prefix . lang('Contest - index', 'title'))  ?>
		</li>
	</ul>
</div>
<div class="module4">
	<div class="title slideMenu"><?php echo lang('part.community') /*this relies on extra info*/ ?></div>
	<ul>
		<?php if (defined('FORUM')): ?>
		<li>
			<?php echo make_link(getPath(FORUM), $prefix . lang('menu.board'), array(), array(), false) ?>
		</li>
		<?php
		endif;
		if ($config['RATES_BY_PAGE'] != -1): //if GuestBook is on ?>
		<li>
			<?php echo make_link('@guestbook', $prefix . lang('GuestBook - index', 'title')) ?>
		</li>
		<?php endif ?>
		<li>
			<?php echo make_link('@polls', $prefix . lang('Poll - index', 'title')) ?>
		</li>
	</ul>
</div>
<?php if (level(LEVEL_ADMIN)): ?>
<div class="module4">
	<div class="title slideMenu"><?php echo lang('part.admin') ?>  </div>
		<ul>
			<li>
				<?php /*@todo move this to interactiv ?*/ echo make_link('@character.search', $prefix . lang('acc.find')) ?>
			</li>
			<li>
				<?php echo make_link('@mass_mail', $prefix . lang('Misc - mass_mail', 'title')) ?>
			</li>
		</ul>
</div>
<?php endif ?>
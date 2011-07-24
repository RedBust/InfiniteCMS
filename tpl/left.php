<?php
//this file is out of layout because you want to modify it manytimes more you
// want to edit the global layout. This avoid mistakes.
//Ce fichier est en-dehors du layout pour vous permettre de modifier le menu en étant sûr de ne pas toucher au reste
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
			<?php echo make_link('@root', '~ ' . lang('News - index', 'title')) ?>
		</li>
		<li>
			<?php echo make_link('@join', '~ ' . lang('Account - join', 'title')) ?>
		<li>
		<?php if (!$connected && $config['ENABLE_REG']): ?>
		<li>
			<?php echo make_link('@register', '~ ' . lang('Account - new', 'title')) ?>
		</li>
		<?php endif ?>
		<li>
			<?php echo make_link('@staff', '~ ' . lang('Misc - staff', 'title')) ?>
		</li>
		<li>
			<?php echo make_link('@tos', '~ ' . lang('menu.rules')) ?>
		</li>
	</ul>
</div>
<br /><br />
<div class="module4">
	<div class="title slideMenu" style="margin-left: 5px;"><?php echo lang('part.server') ?></div>
	<ul>
		<li>
			<?php echo make_link('@cgu_serv', '~ ' . lang('menu.rules')) ?>
		</li>
		<?php if ($config['TEAMSPEAK']['opened']): ?>
		<li>
			<?php echo make_link(array('controller' => 'Misc', 'action' => 'ts'), '~ ' . lang('Misc - ts', 'title')) ?>
		</li>
		<?php endif ?>
		<li>
			<?php echo make_link(array('controller' => 'Misc', 'action' => 'server'), '~ ' . sprintf(lang('menu.infos_comp'), $config['SERVER_NAME'])) ?>
		</li>
	</ul>
</div>
<div class="module4">
	<div class="title slideMenu" style="margin-left: 5px;"><?php echo lang('part.interactif') ?></div>
	<ul>
		<li>
			<?php echo make_link('@ladder', '~ ' . lang('ladder.PvM')) ?>
		</li>
		<?php if ($config['URL_VOTE'] != -1): ?>
		<li>
			<?php echo make_link('@ladder_vote', '~ ' . lang('ladder.vote')) ?>
		</li>
		<?php
		endif;
		if ($config['ENABLE_SHOP']): ?>
		<li>
			<?php echo make_link('@shop', '~ ' . lang('shop.title')) ?>
		</li>
		<?php endif ?>
	</ul>
</div>
<div class="module4">
	<div class="title slideMenu"><?php echo lang('part.community') ?></div>
	<ul>
		<li>
			<?php echo make_link(getPath(FORUM), '~ ' . lang('menu.board'), array(), array(), false) ?>
		</li>
		<?php if ($config['RATES_BY_PAGE'] != -1): //if GuestBook is on ?>
		<li>
			<?php echo make_link('@guestbook', '~ ' . lang('GuestBook - index', 'title')) ?>
		</li>
		<?php endif ?>
		<li>
			<?php echo make_link('@polls', '~ ' . lang('Poll - index', 'title')) ?>
		</li>
		<li>
			<?php echo make_link('@events', '~ ' . lang('Event - index', 'title')) ?>
		</li>
	</ul>
</div>
<?php if (level(LEVEL_ADMIN)): ?>
<div class="module4">
	<div class="title slideMenu"><?php echo lang('part.admin') ?>  </div>
		<ul>
			<li>
				<?php echo make_link('@character.search', lang('acc.find')) ?>
			</li>
			<li>
				<?php echo make_link('@mass_mail', lang('Account - mass_mail', 'title')) ?>
			</li>
		</ul>
</div>
<?php endif ?>
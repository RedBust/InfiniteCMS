					<?php echo render_errors() ?>
					<div class="module<?php echo $connected ? 2 : 1 ?>">
						<?php
						if ($connected): //member is not logged ?
							$unreads = $account->User->getUnreadPM();
							//show infos
							echo lang('pseudo') . ': ' . make_link($account, empty($account->pseudo) ? $account->account : $account->pseudo) .
							 tag('span', array('id' => 'pm_inbox'), $unreads->count() ? '' : '&nbsp;' . make_link('@pm', make_img('icons/email', EXT_PNG, lang('PrivateMessage - index', 'title')), array(), array('data-unless-selector' => '#pm')) ) . tag('br') .
							 ( $config['PASS']['enable'] && $config['ENABLE_SHOP'] ? $account->User->getPoints() . tag('br') : '' ) .
							 lang('level') . ': ' . $account->getLevel() . tag('br') .
							 tag('span', array('id' => 'pm_info'), $account->User->getNextPMNotif()) .
							 make_link(new Account, lang('menu.acc.edit')) . tag('br') .
							 make_link('@sign_off', lang('menu.logout'), array(), array(), false) . tag( 'br' ) .
							 ( $config['URL_VOTE'] != -1 ? make_link('@vote', lang('menu.vote'), array(), array(), false) . tag( 'br' ) : '' ) .
							 ( $config['PASS']['enable'] ? make_link('@credit', lang('menu.credit')) : '' );
						else:
							$pseudo = $router->postVar( Member::CHAMP_PSEUDO );
							echo tag('form', array('method' => 'POST', 'action' => replace_url('@sign_in'), 'id' => 'login_form'),
							   input(Member::CHAMP_PSEUDO, NULL, NULL, $pseudo, array('class' => 'text'))
							 . input(Member::CHAMP_PASS, NULL, 'password', array('class' => 'pwd'))
							 . input('send_login', NULL, 'hidden')
							 . '<input class="ok" type="submit" value="" />');
							if ($config['ENABLE_REG']):
								echo tag('ul', tag('li', make_link(array('controller' => 'Account', 'action' => 'create'), '+ ' . lang('Account - create', 'title'))));
							endif;
						endif;
					?>
					</div>
					<?php if ($connected && $mainChar = $account->getMainChar()): ?>
					<div class="module2 mainChar">
						<div class="title" style="margin-left: 20px;">
							<?php
							echo make_link($mainChar);
							if ($account->Characters->count() > 1)
								echo '&nbsp;', js_link('mainCharSelector.dialog("show")', make_img('user_edit', EXT_PNG), array('controller' => 'Character', 'action' => 'main'));
							?>
						</div>
						<ul>
							<?php if ($mainChar->relatedExists('GuildMember') && $mainChar->GuildMember->relatedExists('Guild')): ?>
							<li id="guildInfo"><?php printf(lang('character._rank_of'), lang('guild.rank.' . $mainChar->GuildMember->rank), make_link($mainChar->GuildMember->Guild)) ?> (lv <?php echo $mainChar->GuildMember->Guild->lvl ?>).<br /></li>
							<?php endif ?>
							<li><?php echo number_format($mainChar->kamas, 0, '.', ' '), 'K / ', $mainChar->capital, ' ', lang('statspoints'), ' / ', $mainChar->spellboost, ' ', lang('spellspoints') ?></li>
							<?php if ($mainChar->alignement != 0): ?>
							<li><?php echo lang('align.' . $mainChar->alignement), ' (', strtolower(lang('align.lvl.' . $mainChar->alvl)), '), ',
										$mainChar->honor, 'h',
										(empty($mainChar->deshonor) ? '' : '/' . $mainChar->deshonor . 'dh'), '.' ?></li>
							<?php endif ?>
						</ul>
					</div>
					<?php endif ?>
					<div id="servInfo">
					</div>
					<?php if ($config['STATS'] && !defined('SKIP_STATS')
					 || (defined('SKIP_STATS') && !SKIP_STATS)): ?>
					<br /><br />
					<div class="module4">
						<div class="title slideMenu" style="margin-left: 20px;"><?php echo lang('part.count') ?></div>
						<?php
						if (level(LEVEL_LOGGED) ? true : $cache = Cache::start('layout_right_stats', '+1 hour')):
						//I don't see any reason for caching when logged? since we already load one table
						// (the biggest memory "blow" is done at the first Doctrine_Core::getTable())
							$created = array();
							$qC = Query::create()
									->select('count(guid) as count')
										->from('Account');
							$c = $qC->fetchOne(Doctrine_Core::HYDRATE_NONE);
							$qC->free();
							$created['acc'] = $c['count'];

							$qC = Query::create()
									->select('count(guid) as count')
										->from('Account')
										->where('logged = 1');
							$c = $qC->fetchOne(Doctrine_Core::HYDRATE_NONE);
							$qC->free();
							$created['logged'] = $c['count'];

							$qC = Query::create()
									->select('count(guid) as count')
										->from('Character');
							$c = $qC->fetchOne(Doctrine_Core::HYDRATE_NONE);
							$qC->free();
							$created['char'] = $c['count'];
						?><ul>
							<li>
								<?php echo tag('b', $created['acc']) . ' ' . pluralize(lang('acc'), $created['acc']) . ' ' . pluralize(lcfirst(lang('created_')), $created['acc'] ) . "\n" ?>
							</li>
							<li>
								<?php echo tag('b', $created['logged']) . ' ' . pluralize(lang('acc'), $created['logged']) . ' ' . pluralize(lcfirst(lang('logged')), $created['char']) . "\n" ?>
							</li>
							<li>
								<?php echo tag('b', $created['char']) . ' ' . pluralize(lang('character'), $created['char']) . ' ' . pluralize(lcfirst(lang('created_')), $created['char']) . "\n" ?>
							</li>
							<li>
								<?php echo make_link('@stats', lang('stats.all')) ?>
							</li>
						</ul>
					</div>
					<?php
							if (!empty($cache))
								$cache->save();
						endif;
					endif;
					?>
					<?php echo render_errors() ?>
					<div class="module<?php echo $connected ? 2 : 1 ?>">
						<?php
						if (!$connected): //member is not logged ?
							if( !$member->isSending() || $errors != array() )
							{ //not sending from / got errors
								$pseudo = $router->postVar( Member::CHAMP_PSEUDO );
								echo tag('form', array('method' => 'POST', 'action' => replace_url('@sign_in'), 'id' => 'login_form'),
								   input(Member::CHAMP_PSEUDO, NULL, NULL, $pseudo, array('class' => 'text'))
								 . input(Member::CHAMP_PASS, NULL, 'password', array('class' => 'pwd'))
								 . input('send_login', NULL, 'hidden')
								 . '<input class="ok" type="submit" value="" />');
							}
							$errors = array();
							if ($config['ENABLE_REG']):
								echo tag('ul', tag('li', make_link('@register', '+ ' . lang('Account - new', 'title'))));
							endif;
						else:
							$unreads = $account->User->getUnreadPM();
							//show infos
							echo lang('pseudo') . ': ' . ( empty($account->pseudo) ? $account->account : $account->pseudo ) .
							  tag('span', array('id' => 'pm_inbox'), $unreads->count() ? '' : '&nbsp;' . make_link('@pm', make_img('icons/email', EXT_PNG, lang('PrivateMessage - index', 'title'))) ) . tag('br') .
							 ( $config['PASS']['enable'] && $config['ENABLE_SHOP'] ? $account->User->getPoints() . tag('br') : '' ) .
							 lang('level') . ': ' . $account->getLevel() . tag('br') .
							 tag('span', array('id' => 'pm_info'), $account->User->getNextPMNotif()) .
							 make_link('@account.edit', lang('menu.acc.edit')) . tag('br') .
							 make_link('@sign_off', lang('menu.logout'), array(), array(), false) . tag( 'br' ) .
							 ( $config['URL_VOTE'] != -1 ? make_link('@vote', lang('menu.vote'), array(), array(), false) . tag( 'br' ) : '' ) .
							 ( $config['PASS']['enable'] ? make_link('@credit', lang('menu.credit')) : '' );
						endif;
					?>
					</div>
					<?php
					if ($connected): //formatting
						echo tag('br') . tag('br') . "\n";
					endif;
					?>
					<div id="servInfo">
					</div>
					<?php if ($config['SHOW_CREATED_STATS']): ?>
					<br /><br />
					<div class="module4">
						<div class="title slideMenu" style="margin-left: 20px;"><?php echo lang( 'part.count' ) ?></div>
						<?php
						if ($cache = Cache::start('layout_right_stats', '+1 hour')):
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
						</ul>
					</div>
					<?php
							$cache->save();
						endif;
					endif;
					?>
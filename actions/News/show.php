<?php
$id = intval($router->requestVar('id', -1));
if ($id == -1)
{
	printf(lang('news.not_exists'), html($id));
	return;
}
if ($cache = Cache::start($router->getController() . '_show_' . $id .
	 '_' . (int)level(LEVEL_ADMIN)))
{
	if (!( $news = NewsTable::getInstance()->createQuery('n')
							->where('n.id = ?', $id)
							->leftJoin('n.Author u')
								->leftJoin('u.Account a')
							->leftJoin('n.Comments c')
								->leftJoin('c.Author au')
									->leftJoin('au.Account ac')
							->fetchOne() ))
	{
		printf(lang('news.not_exists'), html($id));
		return;
	}

	jQ('
var acc = $("#comments");
acc.accordion(
{
	collapsible: true,
	fillSpace: true
} ).sortable(
	{
		axis: "y",
		handle: "h3"
} );
binds.add(function ()
{
	delete acc;
});');
	$com_actual = 0;
	$coms = '';
	foreach ($news->Comments as $com)
	{
		/* @var $com Comment */
		if ($config['MAX_COMMENTS'] != -1 && ++$com_actual > $config['MAX_COMMENTS'])
			break; //stop the foreach if we reached the limit
		$date = explode(' ', $com['created_at']);
		$date = sprintf(lang('at'), $date[0], $date[1]);
		if ($com->relatedExists('Author') && $com->Author->relatedExists('Account'))
			$author = sprintf(lang('by'), make_link(array('controller' => 'Account', 'action' => 'show', 'id' => $com->Author->guid), $com->Author->Account->pseudo));
		$coms = tag('h3', array('class' => 'comment-date'), '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . tag('span', array('class' => 'comment-title'), $com['title']) . ' - <i>' .
						sprintf(lang('created'), $date) . ( empty($author) ? '' : ' ' . $author ) . '</i>' .
						( level(LEVEL_ADMIN) ? make_link(to_url(array('controller' => $router->getController(), 'action' => 'comment', 'mode' => 'censor', 'id' => $com['id'])), lang('censor'), array(), array(), false) : '')) .
				tag('div', array('class' => 'comment-content', 'data-id' => $com['id'], 'style' => 'height: 220px !important;'), nl2br(News::format($com['content']))) . $coms; //xss protection already done
	}
	unset($com);
	$coms = sprintf('
		<div id="comments">
			%s
		</div>', $coms);
	$comsTitle = pluralize(lang('news.com.title'), count($news->Comments));
	$isLogged = level(LEVEL_LOGGED);
	$canComment = !$isLogged || $config['MAX_COMMENTS'] == 0 ? false : $account->User->canComment($news);
	printf('<!--%s-->
				<div class="post">
					<div class="content">
						<div class="infos">
							<div class="title">
								%s
							</div>
							<div class="autre">
								%s. %s. %s
							</div>
						</div>
						<div align="center"><br />
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;%s
						</div><br /><br />
					</div>
					%s
					<div id="comment">
						<h1>%s</h1>
						<div id="form_coms">
							%s
						</div>
						%s<br />
					</div>
					<!-- -->
				</div>', make_link(array('controller' => $router->getController(), 'action' => 'index'), lang('back_to_index')),
			$news->buildLinks(),
			sprintf(lang('by'), $news->relatedExists('Author') && $news->Author->relatedExists('Account') ? make_link($news->Author->Account) : '?'), sprintf(lang('created'), $news['created_at']), $news['updated_at'] && $news['updated_at'] != $news['created_at'] ? sprintf(lang('last_update'), $news['updated_at']) : '',
			News::format($news['content']),
			$config['MAX_COMMENTS'] == 0 ? '<!--' : '', //comments are disabled
			( $canComment ? js_link('$( "#form_coms").slideToggle(); $("#comments").slideToggle();', $comsTitle) : ( count($news->Comments) ? $comsTitle : '' )),
			( $canComment ? make_form(array(
				array('title', ucfirst(lang('title')) . ':<br />', NULL, strip_tags($news->title)),
				array('comment', ucfirst(lang('news.com.title')) . ':<br />', 'textarea', NULL, array('cols' => 20, 'rows' => 10)),
			), to_url(array('controller' => 'Comment', 'action' => 'create', 'id' => $news->id))) : ''), $coms);


	$cols = array('content'); //columns for edit in place in comments. Note: impossible with title. BELIEV ME FOOLS, I ALREADY TRIED §§
	if (level(LEVEL_ADMIN))
	{
		$url = to_url(array(//URL for editInPlace of the title of the news
					'controller' => $router->getController(),
					'action' => 'update',
					'col' => 'title',
					'output' => 0,
					'id' => $news->id,
		), false);
		$url_com = array(
			'_base' => array(//url for editInPlace of the content of a comment
				'controller' => 'Comment',
				'action' => 'update',
				'mode' => 'update',
				'mod' => 1, //moderator action
				'output' => 0,
				'id' => '%%t.data("id")%%',
			),
		);
		foreach ($cols as $col)
		{
			$url_com[$col] = to_url($url_com['_base'] + array(
				'col' => $col,
			), false);
		}
		jQ('
	$( "#newsTitle" ).editInPlace(
	{
		url: "' . $url . '",
	} );');
	}
	if ($config['MAX_COMMENTS'] != 0)
	{
		foreach ($cols as $col)
		{
			jQ('
$( ".comment-' . $col . '" ).each( function()
	{
		//t is used by $url_com[$col]
		t = $( this );
		' . ( level(LEVEL_ADMIN) ? '
		t.editInPlace(
		{
			url: "' . $url_com[$col] . '",
			field_type: "textarea"
		} );' : '' ) . '
		t.parent().resizable(
		{
			resize: function()
			{
				acc.accordion( "resize" );
			},
		} );
	} );');
		}
	}
	$cache->save();
}
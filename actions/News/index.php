<?php
if ($cache = Cache::start($router->getController() . '_index_' . intval($p = $router->requestVar('id', 0)) . '_' .
	(int)level(LEVEL_ADMIN) . '_' . $config['ARTICLES_BY_PAGE']))
{
	$newsDql = Query::create()
					->from('News n')
						->leftJoin('n.Author u')
							->leftJoin('u.Account a')
						->leftJoin('n.Comments c')
					->orderBy('n.created_at DESC');
	/* @var $newsDql Query */
	$pager = new Doctrine_Pager($newsDql, $p, $config['ARTICLES_BY_PAGE']);
	$news = $pager->execute();
	$layout = new Doctrine_Pager_Layout($pager, new Doctrine_Pager_Range_Sliding(array('chunk' => 4)), to_url(array('controller' => $router->getController(), 'action' => $router->getAction(), 'id' => '')));
	$layout->setTemplate('[<a href="{%url}" class="link">{%page}</a>]');
	$layout->setSelectedTemplate('[<b>{%page}</b>]');
	$count = count($news);
	$i = 0;
	if ($count > 0)
	{
		foreach ($news as $new)
		{
			/* @var $new News */
			$content = News::format($new['content']);
			printf('
				<div class="post">
					<div class="content">
						<div class="infos">
							<div class="title" id="title%d">
								%s
							</div>
							<div class="autre">%s | %s. %s %s</div>
						</div>
						<div align="center" id="cnt%d" class="cont">
							&nbsp;&nbsp;%s
						</div>
					</div>
				</div>',
					$new['id'], $new->buildLinks(),
					$new->getAuthorString(), sprintf(lang('created'), $new['created_at']), $new['updated_at'] && $new['updated_at'] != $new['created_at'] ? sprintf(lang('last_update'), $new['updated_at']) : '', pluralize(lang('news.com.title'), count($new['Comments']), true),
					$new['id'], $content);
		}
		echo paginate($layout);
		unset($new);
	}
	else
		echo '<p align="center">', lang('news.any'), '</p><br />';

	if (level(LEVEL_ADMIN))
		echo '<br />' . make_link(array('action' => 'update'), lang('act.news_new'));

	$cache->save();
	$news->free();
	unset($pager, $layout);
}
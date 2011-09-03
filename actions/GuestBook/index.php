<?php
if ($config['RATES_BY_PAGE'] == -1)
	return;

$reviewsDql = Query::create()
				->from( 'Review r' )
					->leftJoin('r.Author u')
						->leftJoin('u.Account a')
				->orderBy('r.created_at DESC');
/* @var $newsDql Query */
$pager = new Doctrine_Pager($reviewsDql, $router->requestVar('id', 0), $config['RATES_BY_PAGE']);
$reviews = $pager->execute();
$affichage = new Doctrine_Pager_Layout( $pager, new Doctrine_Pager_Range_Sliding(array('chunk' => 4)), to_url(array('controller' => $router->getController(), 'action' =>  $router->getAction(), 'id' => '')));
$affichage->setTemplate('[<a href="{%url}" class="link">{%page}</a>]');
$affichage->setSelectedTemplate('[<b>{%page}</b>]');
if ($reviews->count())
{
	foreach ($reviews as $review)
	{
		printf( '
			<div class="post" id="review%d">
				<div class="content">
					%s
					<div class="infos">
						<div class="autre">%s | %s.</div>
					</div>
					<div align="center" class="cont">
						&nbsp;&nbsp;%s
					</div>
				</div>
			</div>',
		 $review['id'],
		 level(LEVEL_ADMIN) ? tag('div', array('class' => 'title'), make_link(array(
					'controller' => $router->getController(),
					'action' => 'delete',
					'id' => $review['id'],
			), lang('act.delete'), NULL, array(), false))  : '',
		 sprintf(lang('by'), $review->relatedExists('Author') && $review->Author->relatedExists('Account') ? make_link($review->Author->Account) : '' ),
		 sprintf(lang('created'), $review['created_at']),
		 $review['comment']);
	}
	echo paginate($layout);
}
else
	echo lang('rate.any');
echo '<br />';
if ($member->isConnected())
{
	if ($account->User->canReview())
		echo make_link('@guestbook.new', lang('rate.create'));
}
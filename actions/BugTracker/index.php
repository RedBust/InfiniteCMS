<?php
if( !level( LEVEL_ADMIN ) )
	return;

$bugsDql = Query::create()
				->from( 'Bugs b' )
					->leftJoin( 'b.User u' )
				->where( 'b state != ?', STATE_RESOLVED )
					->orderBy( 'b.created_at ASC' );
$pager = new Doctrine_Pager( $bugsDql, News::actualPage(), $config['BUGS_BY_PAGE'] );
$bugs = $pager->execute( array(), Doctrine_Core::HYDRATE_ARRAY );
$affichage = new Doctrine_Pager_Layout( $pager, new Doctrine_Pager_Range_Jumping( array( 'chunk' => 4 ) ), to_url( array( 'controller' => $router->getController(), 'action' => $router->getAction(), 'page' => '' ) ) );
$affichage->setTemplate( '[<a href="{%url}">{%page}</a>]' );
$affichage->setSelectedTemplate( '[<b>{%page}</b>]' );

$authorsId = array();
foreach( $bugs as $bug )
{
	$authorsId[] = $bug['author_id'];
}
$authors = Query::create()
				->from( 'Account INDEXBY guid' )
					->whereIn( 'guid', $authorsId )
				->fetchArray();

echo '
<table>' . tag( 'tr',
 tag( 'td', tag( 'b', sprintf( lang( 'created_at' ) ) ) ) .
 tag( 'td', tag( 'b', sprintf( lang( 'by_desc' ) ) ) ) .
 tag( 'td', tag( 'b', lang( 'title' ) ) ) );
foreach( $bugs as $bug )
{
	$date = explode( ' ', $bug['created_at'] );
	echo tag( 'tr',
	 tag( 'td', sprintf( lang( 'at' ), $date[0], $date[1] ) ) .
	 tag( 'td', $authors[$bug['author']]['pseudo'] ) .
	 tag( 'td', $bug['title'] ) );
}
unset( $bug );
echo '
</table>';
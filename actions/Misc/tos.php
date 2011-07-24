<?php
echo tag('div', array('id' => 'tos'),
 sprintf( lang( 'tos' ), $config['SERVER_NAME'], $config['SERVER_EMAIL'] ) );
jQ( '
$("#tos").accordion({ collapsible: true });
$( ".sub_tit" ).each( function ()
{ //auto-tabulate
	$(this).html("&nbsp;&nbsp;&nbsp;&nbsp; " + $(this).html());
});' );
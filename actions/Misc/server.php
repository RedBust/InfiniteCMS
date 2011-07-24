<?php
printf('&copy; %s %s<br />2009 - %d', $config['SERVER_NAME'],
 ( !empty( $config['SERVER_CORP'] ) ? ' &bull; ' . $config['SERVER_CORP'] : '' ),
 date('Y'));
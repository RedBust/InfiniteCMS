<?php
$router->codeUnless(404, $pollOption = PollOptionTable::getInstance()->find($id = $router->requestVar('id', -1)));

$url = to_url($pollOption);
$pollID = $pollOption->poll_id;
$pollOption->delete();

redirect($url);
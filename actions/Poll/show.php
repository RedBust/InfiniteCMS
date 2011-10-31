<?php
$router->codeUnless(404, $poll = PollTable::getInstance()->find($id = $router->requestVar('id', -1)));

partial('_show', array('poll'), PARTIAL_CONTROLLER);
echo tag('br') . make_link('@polls', lang('back_to_list'));
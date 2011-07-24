<?php
if (!( $poll = PollTable::getInstance()->find($id = $router->requestVar('id', -1)) ) )
{
	printf(lang('poll.not_exists'), html($id));
	return;
}
partial('_show', array('poll'), PARTIAL_CONTROLLER);
echo tag('br') . make_link('@polls', lang('back_to_list'));
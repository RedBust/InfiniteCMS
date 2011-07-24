<?php
if ($config['URL_VOTE'] == -1)
{
	echo lang('acc.vote_desactived');
	return;
}

if (level(LEVEL_LOGGED))
{
	//move vote time to a config variable ? Dunno ...
	// Rpg-Paradize is the most-used Private Servers Ladder and if I change this
	// I need to change the url for vote, the image ... too lazy
	if (!empty($account->User->lastVote) && !date_passed($account->User->lastVote, '+6 hours'))
		echo lang('acc.vote.already');
	else
	{
		$account->User->lastVote = time();
		$account->User->points += $config['POINTS_VOTE'];
		$account->User->votes += 1;
		$account->User->save();
		echo sprintf(lang('acc.vote.won'), $config['POINTS_VOTE']);
	}
	echo '<br /><br />';
}

echo make_link($config['URL_VOTE'], make_img('http://www.rpg-paradize.com/vote', EXT_GIF));
redirect($config['URL_VOTE'], 3);
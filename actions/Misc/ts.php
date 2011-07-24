<?php
if (!$config['TEAMSPEAK']['opened'])
{
	echo lang('ts.not_exists');
	return;
}
?><table>
	<tr>
		<td>
			<b><?php echo lang('host') ?>:</b>
		</td>
		<td>
			<i><?php echo $config['TEAMSPEAK']['server'] ?></i>
		</td>
	</tr>
	<tr>
		<td>
			<b><?php echo lang('port') ?>:</b>
		</td>
		<td>
			<i><?php echo $config['TEAMSPEAK']['port'] ?></i>
		</td>
	</tr>
	<tr>
		<td>
			<b><?php echo lang('total') ?>:</b>
		</td>
		<td>
			<i><?php echo $config['TEAMSPEAK']['server'] . ':' . $config['TEAMSPEAK']['port'] ?></i>
		</td>
	</tr>
	<?php if (!empty($config['TEAMSPEAK']['pass'])): ?>
	<tr>
		<td>
			<b><?php echo lang('acc.register.password') ?>:</b>
		</td>
		<td>
			<i><?php echo $config['TEAMSPEAK']['pass'] ?></i>
		</td>
	</tr>
	<?php endif ?>
</table>
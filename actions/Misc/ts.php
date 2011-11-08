<?php $router->codeUnless(404, $config['TEAMSPEAK']['ENABLE']) ?><table>
	<tr>
		<td>
			<b><?php echo lang('host') ?>:</b>
		</td>
		<td>
			<i><?php echo $config['TEAMSPEAK']['SERVER'] ?></i>
		</td>
	</tr>
	<?php if (!empty($config['TEAMSPEAK']['PORT'])): ?>
	<tr>
		<td>
			<b><?php echo lang('port') ?>:</b>
		</td>
		<td>
			<i><?php echo $config['TEAMSPEAK']['PORT'] ?></i>
		</td>
	</tr>
	<?php endif ?>
	<tr>
		<td>
			<b><?php echo lang('total') ?>:</b>
		</td>
		<td>
			<i><?php echo $config['TEAMSPEAK']['SERVER'] . ':' . $config['TEAMSPEAK']['PORT'] ?></i>
		</td>
	</tr>
	<?php if (!empty($config['TEAMSPEAK']['PASS'])): ?>
	<tr>
		<td>
			<b><?php echo lang('acc.register.password') ?>:</b>
		</td>
		<td>
			<i><?php echo $config['TEAMSPEAK']['PASS'] ?></i>
		</td>
	</tr>
	<?php endif ?>
</table>
<?php
if (count($config['staff']))
{
	echo '
	<table>';
	$staffProfils = array('id' => array(), 'pseudo' => array());
	foreach ($config['staff'] as $id => $foo)
	{
		if (is_integer($id))
			$staffProfils['id'][] = $id;
		/* v2: { 'guid@5': ['Fondateur', 'Dev. client'], 'pseudo@Nami': ['Flemmard', 'Glandeur'] }
		  $id = explode( '@', $id );
		  if( ( strlen( $id ) - strlen( trim( $id, '\\' ) ) ) % 2 ) //not escaped
		  { //no need to re-explode: there is any columns with \\
		  $staffProfils[$id[0]][] = $id[1];
		  $staff[$id] = $id[1];
		  }
		 */
	}
	$staffProfil = Query::create()
					->from('Account INDEXBY guid')
						->whereIn('guid', $staffProfils['id'])
					->execute();
	/* v2
	  $staffProfils = Query::create()
	  ->from( 'Account' )
	  foreach( $staffProfils as $key => $vals )
	  $staffProfils->orWhereIn( $key, $vals );
	  $staffProfils = $staffProfils->execute();
	 */
	foreach ($config['staff'] as $name => $rang)
	{
		if (isset($staffProfil[$name]))
			$name = $staffProfil[$name]->getProfilLink();
		/* v2
		  foreach( $staffProfils as $key => $foo )
		  if( isset( $staffProfils[$key][$name] ) )
		  {
		  $name = $staffProfils[$key][$name]->getProfilLink();
		  break;
		  }
		 */

		echo '
		<tr>
			<td>
				<b>
					' . $name . '
				</b>
			</td>
			<td>
				<i>' . implode('</i>, <i>', (array) $rang) . '</i>
			</td>
		</tr>';
	}
	echo '
	</table>';
}
else
{
	echo lang('staff_empty');
}
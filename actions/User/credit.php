<?php
if (!check_level(LEVEL_LOGGED))
	return;

if (!$config['PASS']['enable'])
{
	echo lang('acc.credit.disabled');
	return;
}

//page?
$isSubmitted = false;
switch ($config['PASS']['type'])
{
	case 'webo':
		$isSubmitted = $router->requestVar('code') != NULL;
		break;
	case 'star':
		$isSubmitted = $router->requestVar('code1') != NULL;
		break;
	default:
		throw new Exception(sprintf('Invalid paymode %s !', $config['PASS']['type']));
}

$inputAttr = array();
if ($isSubmitted)
{
	//code validation
	switch ($config['PASS']['type'])
	{
		case 'webo':
			$url = 'http://payer.webopass.fr/valider_code.php' .
					 to_url(array(
						'cc' => $config['PASS']['cc'],
						'document' => $config['PASS']['document'],
						'requete' => 1,
						'code' => urlencode($router->postVar('code', '')),
						'no_saisie_code' => 1,
					 ), false);
			$result = @file($url);
			$isValidCode = $result && trim($result[0]) === 'OUI';
			break;
		case 'star':
			$values = $router->postVars('idp', 'code1', 'DATAS');
			$result = @file_get_contents('http://script.starpass.fr/check_php.php' . to_url(array(
								//idp;ids;idd
								'ident' => sprintf('%s;;%s', $values['idp'], $config['PASS']['idd']),
								'codes' => $values['code1'],
								'DATAS' => $values['DATAS'],
							), false, false));
			//Starpass put here a totally un-needed (for me) "explode" ...
			// I just need "validCode?", not alot of informations ... And it's better.
			$isValidCode = $result && substr($result, 0, 3) === 'OUI';
			break;
	}
	if ($isValidCode)
	{
		//add points & count
		$account->User['points'] += $config['POINTS_CREDIT' . (level(LEVEL_VIP) ? '_VIP' : '')];
		$account->User['audiotel'] = intval($account->User['audiotel']) + 1;
		echo lang('acc.credit.credited');
	}
	else
		echo lang('acc.credit.code_invalid');
}
else
{
	echo tag('h1', ucfirst(lang('acc.credit.add')));
	//code submit
	switch ($config['PASS']['type'])
	{
		case 'webo':
			//script totally re-coded ... I hate code repetition
			$txt = '
<table cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td background="http://payer.webopass.fr/img/skins/1/header.gif" width="313" height="63"></td>
	</tr>
	<tr>
		<td background="http://payer.webopass.fr/img/skins/1/texte.jpg" width="313" height="68"></td>
	</tr>
	<tr>
		<td background="http://payer.webopass.fr/img/skins/1/cellule_centre.gif" width="313" align="center">
			<table cellpadding="2" cellspacing="2">
				<tr>';

			$list_pays = array(
				'France', 'Luxembourg', 'Belgique', 'Suisse',
				'Canada', 'Pays Bas', 'Royaume Unis', 'Espagne',
				'Honk Kong', 'Allemagne', 'Nouvelle Zelande', 'Italie',
				'Australie', 'Autriche', 'USA',
			);
			$i = 0;
			foreach ($list_pays as $pays)
			{
				if (!( $i++ % 4 ))
					$txt .= '
				</tr>
				<tr>';
				$txt .= sprintf('
					<td align="center">
						<a href="#" onclick="window.open(\'http://payer.webopass.fr/affiche_paiement.php?cc=%1$s&document=%2$s&skin=1&pays=%13s\',\'\',\'toolbar=no,location=no,directories=no,status=no,menubar=no, ?scrollbars=yes,resizable=no,copyhistory=no,width=650,height=550\');" style="text-decoration: none;">
							<img src="http://www.webopass.fr/img/drapeaux/%3$s_flag.jpg" width="22" height="18" alt="%3$s" border="0"><br />
							<font style="font-size: 10px; font-face: Verdana; color: black; text-decoration: underline;">
								%3$s
							</font>
						</a>
					</td>', $config['PASS']['cc'], $config['PASS']['document'], $pays);
			}
			$txt .= <<<WEBO
				</tr>
			</table>
			<a href="#" onClick="window.open('http://payer.webopass.fr/affiche_paiement.php?cc=%1\$s&document=%2\$s&skin=1&pays=Tous pays','','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars= ?yes,resizable=no,copyhistory=no,width=650,height=550');">
				<img src="http://payer.webopass.fr/img/skins/1/bouton.gif" width="313" height="22" alt="Tous pays" border="0">
				</a><br /><br />
		</td>
	</tr>
	<tr>
		<td background="http://payer.webopass.fr/img/skins/1/footer.gif" width="313" height="22" align="center">
			<a href="#" onClick="window.open('http://payer.webopass.fr/plus_dinfos_paiement.php?cc=%1\$s&document=%2\$s&skin=1','','toolbar=no,location=no,directories=no,status=no,menubar=no,scrol ?lbars=yes,resizable=no,copyhistory=no,width=650,height=550');">
				<font style="font-size: 10px; font-face: Verdana; color: black; text-decoration: underline;">
					%3\$s
				</font>
			</a>
		</td>
	</tr>
</table>
WEBO;
			printf($txt, $config['PASS']['cc'], $config['PASS']['document'],
					lang('acc.credit.more_info_credit'));
			echo tag('br') .
			make_form(array(
				array('code', lang('acc.credit.code') . tag('br'), NULL,
					NULL, $inputAttr),
			 ), APPEND_FORM_TAG);
			break;
		case 'star':
			echo tag('div', array('id' => 'starpass_' . $config['PASS']['idd']), '') .
			tag('noscript', lang('please_enable_js'));
			javascript_tag(make_url('http://script.starpass.fr/script.php', array(
						'idd' => $config['PASS']['idd'],
						'verif_en_php' => 1,
						'datas' => '',
			 ), false));
#			$inputAttr['id'] = sprintf('starpass_%d', $config['PASS']['idd']);
			break;
	}
}
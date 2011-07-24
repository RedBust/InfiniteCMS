<?php jQ('

	var loc306 = $("#link_306");

	var action306 = function (event)
	{
		newsEditPanel( 2 );
		event.preventDefault();
	};
locations["link_306"] = "/Infinite/News/update/2";
loc306.attr("href", "#").live( "click", action306 );

var profils = {},
	profilBox = $("#profil");
profilBox.dialog($.extend(dialogOpt, {"modal": false}));
function showProfil(id)
{
	profilBox
		.find("div")
		.hide();
	profilBox
		.find("#profil-" + id)
		.show();
	profilBox.dialog("open");
}
binds.add(function (undef)
{
	delete profils;
	if (profilBox != undef)
	{
		profilBox.dialog("close");
		delete profilBox;
	}
	delete showProfil;
});
profilBox.append($("#profil-2"));

	var loc5290 = $("#link_5290");

	var action5290 = function (event)
	{
		showProfil(2);;
		event.preventDefault();
	};
locations["link_5290"] = "/Infinite/Account/show/2";
loc5290.attr("href", "#").live( "click", action5290 );

	var loc3304 = $("#link_3304");

	var action3304 = function (event)
	{
		newsEditPanel( 1 );
		event.preventDefault();
	};
locations["link_3304"] = "/Infinite/News/update/1";
loc3304.attr("href", "#").live( "click", action3304 );
profilBox.append($("#profil-1"));

	var loc4823 = $("#link_4823");

	var action4823 = function (event)
	{
		showProfil(1);;
		event.preventDefault();
	};
locations["link_4823"] = "/Infinite/Account/show/1";
loc4823.attr("href", "#").live( "click", action4823 );');  ?><div id="profil" style="display: none;" title="Informations"></div><div id="profil-2" style="display: none;"><b>Pseudo: </b>Anthony<br /><span class="f_level" data-id="2"><b><i><u>Administrateur</u></i></b></span><br /><br /><b>Dernière connexion: </b>Le 2011-07-06 21:35:34<br /><a class="link" href="/Infinite/Account/show/2">Plus ...</a></div> 
					<div class="post">
						<div class="content">
							<div class="infos">
								<div class="title" id="title2">
									<a class="link" href="/Infinite/News/show/2"><span id="newsTitle">vive le roi !</span></a>
								</div>
								<div class="autre">Par <i><a href="/Infinite/Account/show/2" id="link_5290">Anthony</a></i> | Le 2011-07-06 19:41:36.  0 Commentaire</div>
							</div>
							<div align="center" id="cnt2" class="cont">
								&nbsp;&nbsp;<div style="text-align: center;"><strong>Le roi est mort, vive le roi !</strong></div>
							</div>
						</div>
					</div><div id="profil-1" style="display: none;"><b>Pseudo: </b>Vendethiel<br /><span class="f_level" data-id="1"><b><i><u>Administrateur</u></i></b></span><br /><br /><b>Dernière connexion: </b>Le 2011-06-16 01:38:07<br /><a class="link" href="/Infinite/Account/show/1">Plus ...</a></div> 
					<div class="post">
						<div class="content">
							<div class="infos">
								<div class="title" id="title1">
									<a class="link" href="/Infinite/News/show/1"><span id="newsTitle">Mon super article ;-)</span></a>
								</div>
								<div class="autre">Par <i><a href="/Infinite/Account/show/1" id="link_4823">Vendethiel</a></i> | Le 2011-06-15 23:50:04. Dernière modification le 2011-07-06 17:51:43. 1 Commentaire</div>
							</div>
							<div align="center" id="cnt1" class="cont">
								&nbsp;&nbsp;CC ^__^<br />&nbsp;:3<br /><img title="Kiss" src="/Infinite/static/tiny_mce/plugins/emotions/img/smiley-kiss.gif" alt="Kiss" border="0" /><br />
							</div>
						</div>
					</div> <?php global $router, $connected, $account;
if($c = Cache::start("Account_show_profil_2_" . ($connected ? $account->guid : -1)))
{
	global $accountId;
	$accountId = 2;
	echo tag("div", array(
			"id" => "profil-2",
			"style" => "display: none;",
		), require $router->getPath("Account", "show"));
	$c->save(Cache::SHOW, Cache::NO_JS);
}
?><?php global $router, $connected, $account;
if($c = Cache::start("Account_show_profil_1_" . ($connected ? $account->guid : -1)))
{
	global $accountId;
	$accountId = 1;
	echo tag("div", array(
			"id" => "profil-1",
			"style" => "display: none;",
		), require $router->getPath("Account", "show"));
	$c->save(Cache::SHOW, Cache::NO_JS);
}
?>
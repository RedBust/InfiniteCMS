<?php jQ('

	var acc = $( "#comments" );
	acc.accordion(
		{
			collapsible: true,
			fillSpace: true
		} ).sortable(
			{
				axis: "y",
				handle: "h3"
		} );
	binds.add(function ()
		{
			delete acc;
		});

	var loc2788 = $("#link_2788");

	var action2788 = function (event)
	{
		newsEditPanel( 1 );
		event.preventDefault();
	};
locations["link_2788"] = "/Infinite/News/update/1";
loc2788.attr("href", "#").live( "click", action2788 );

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
profilBox.append($("#profil-1"));

	var loc4042 = $("#link_4042");

	var action4042 = function (event)
	{
		showProfil(1);;
		event.preventDefault();
	};
locations["link_4042"] = "/Infinite/Account/show/1";
loc4042.attr("href", "#").live( "click", action4042 );

		$( ".comment-content" ).each( function()
			{
				//t is used by $url_com[$col]
				t = $( this );
				
				t.parent().resizable(
					{
						resize: function()
						{
							acc.accordion( "resize" );
						},
					} );
			} );');  ?><div id="profil" style="display: none;" title="Informations"></div><div id="profil-1" style="display: none;"><b>Pseudo: </b>Vendethiel<br /><span class="f_level" data-id="1"><b><i><u>Administrateur</u></i></b></span><br /><br /><b>Dernière connexion: </b>Le 2011-06-16 01:38:07<br /><a class="link" href="/Infinite/Account/show/1">Plus ...</a></div> <!--<a class="link" href="/Infinite/News/index">Revenir à l'acceuil</a>-->
				<div class="post">
					<div class="content">
						<div class="infos">
							<div class="title">
								<span id="newsTitle">Mon super article ;-)</span>
							</div>
							<div class="autre">
								Par <i><a href="/Infinite/Account/show/1" id="link_4042">Vendethiel</a></i>. Le 2011-06-15 23:50:04. Dernière modification le 2011-07-06 17:51:43.
							</div>
						</div>
						<div align="center"><br />
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CC ^__^<br />&nbsp;:3<br /><img title="Kiss" src="/Infinite/static/tiny_mce/plugins/emotions/img/smiley-kiss.gif" alt="Kiss" border="0" /><br />
						</div><br /><br />
					</div>
					
					<div id="comment">
						<h1>Commentaire</h1>
						<div id="form_coms">
							
						</div>
						
		<div id="comments">
			<h3 class="comment-date">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="comment-title">Mon super article ;-)</span> - <i>Le 2011-07-06 à 17:37:59 Par <i><a class="link" href="/Infinite/Account/show/1">Vendethiel</a></i></i></h3><div class="comment-content" data-id="1" style="height: 220px !important;"><br /><br /><br /><br /><br />cc<br /><br /><br /></div>
		</div><br />
					</div>
					<!-- -->
				</div> <?php global $router, $connected, $account;
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
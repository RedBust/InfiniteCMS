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

	var loc5574 = $("#link_5574");

	var action5574 = function (event)
	{
		newsEditPanel( 2 );
		event.preventDefault();
	};
locations["link_5574"] = "/Infinite/News/update/2";
loc5574.attr("href", "#").live( "click", action5574 );

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

	var loc9809 = $("#link_9809");

	var action9809 = function (event)
	{
		showProfil(2);;
		event.preventDefault();
	};
locations["link_9809"] = "/Infinite/Account/show/2";
loc9809.attr("href", "#").live( "click", action9809 );

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
			} );');  ?><div id="profil" style="display: none;" title="Informations"></div><div id="profil-2" style="display: none;"><b>Pseudo: </b>Anthony<br /><span class="f_level" data-id="2"><b><i><u>Administrateur</u></i></b></span><br /><br /><b>Dernière connexion: </b>Le 2011-07-06 21:35:34<br /><a class="link" href="/Infinite/Account/show/2">Plus ...</a></div> <!--<a class="link" href="/Infinite/News/index">Revenir à l'acceuil</a>-->
				<div class="post">
					<div class="content">
						<div class="infos">
							<div class="title">
								<span id="newsTitle">vive le roi !</span>
							</div>
							<div class="autre">
								Par <i><a href="/Infinite/Account/show/2" id="link_9809">Anthony</a></i>. Le 2011-07-06 19:41:36. 
							</div>
						</div>
						<div align="center"><br />
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style="text-align: center;"><strong>Le roi est mort, vive le roi !</strong></div>
						</div><br /><br />
					</div>
					
					<div id="comment">
						<h1></h1>
						<div id="form_coms">
							
						</div>
						
		<div id="comments">
			
		</div><br />
					</div>
					<!-- -->
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
?>
<?php jQ('

var newsPanel = $( "#news_edit" ), f = true;
newsPanel.dialog( $.extend( dialogOpt, {"modal": false } ) );
function newsEditPanel(id)
{
	var cont = $( "#form_content_ifr" ).find( "html > body" ).html();
	if( cont === "" || cont === null )
	{
		updateContent( locations[$( ".edit_link_" + id ).attr( "id" )] );
		return;
	}
	newsPanel.find( "div" ).hide();
	newsPanel.find( "#news_panel-" + id ).show();
	newsPanel.dialog( "open" );
	
	if( f )
	{
		tinymce_include();
		f = false;
	}
}
bind( function () { newsPanel.dialog( "close" ); } );
newsPanel.append( $( "#news_panel-2" ) );

	var loc2865 = $("#link_2865");

	var action2865 = function (event)
	{
		newsEditPanel( 2 );
		event.preventDefault();
	};
locations["link_2865"] = "/Infinite/News/update/2";
loc2865.attr("href", "#").live( "click", action2865 );

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

	var loc6431 = $("#link_6431");

	var action6431 = function (event)
	{
		showProfil(2);;
		event.preventDefault();
	};
locations["link_6431"] = "/Infinite/Account/show/2";
loc6431.attr("href", "#").live( "click", action6431 );
newsPanel.append( $( "#news_panel-1" ) );

	var loc6083 = $("#link_6083");

	var action6083 = function (event)
	{
		newsEditPanel( 1 );
		event.preventDefault();
	};
locations["link_6083"] = "/Infinite/News/update/1";
loc6083.attr("href", "#").live( "click", action6083 );
profilBox.append($("#profil-1"));

	var loc7414 = $("#link_7414");

	var action7414 = function (event)
	{
		showProfil(1);;
		event.preventDefault();
	};
locations["link_7414"] = "/Infinite/Account/show/1";
loc7414.attr("href", "#").live( "click", action7414 );');  ?><div title="Index - edit (Untranslated)" id="news_edit" style="display: none;"></div><div id="news_panel-2"><form method="POST" action="/Infinite/Index/update/2" id="form"><label for="form_title">Titre<br /></label><input type="text" name="title" value="vive le roi !" id="form_title" /><br /><label for="form_content">Contenu<br /></label><textarea name="content" id="form_content"><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
&lt;/head>
<body>
<p style="text-align: center;"><strong>Le roi est mort, vive le roi !&lt;/strong>&lt;/p>
&lt;/body>
&lt;/html></textarea><br /><input type="submit" name="send" value="Envoyer" id="form_send" /><input type="hidden" name="sent" value="1" id="form_sent" /></form></div><div id="profil" style="display: none;" title="Informations"></div><div id="profil-2" style="display: none;"><b>Pseudo: </b>Anthony<br /><span class="f_level" data-id="2"><b><i><u>Administrateur</u></i></b></span><br />Points: <span class="f_points" data-id="2">534545</span><br /><br /><b>Dernière connexion: </b>Le 2011-07-06 21:35:34<br /><a class="link" href="/Infinite/Account/edit/2">Modifier le compte de Anthony</a>&nbsp;&bull;&nbsp;<a class="link" href="/Infinite/Account/index/2">Modifier les informations de Anthony</a><br /><a class="link" href="/Infinite/Account/show/2">Plus ...</a></div> 
				<div class="post">
					<div class="content">
						<div class="infos">
							<div class="title" id="title2">
								<a class="link" href="/Infinite/News/show/2"><span id="newsTitle">vive le roi !</span></a>  <a class="edit_link_2" href="/Infinite/News/update/2" id="link_2865"><img src="/Infinite/static/templates/default/images/bouton/btn_edit.png" title="" alt="" /></a> <a class="link" href="/Infinite/News/delete/2"><img src="/Infinite/static/templates/default/images/bouton/btn_delete.png" title="" alt="" /></a>
							</div>
							<div class="autre">Par <i><a href="/Infinite/Account/show/2" id="link_6431">Anthony</a></i> | Le 2011-07-06 19:41:36.  0 Commentaire</div>
						</div>
						<div align="center" id="cnt2" class="cont">
							&nbsp;&nbsp;<div style="text-align: center;"><strong>Le roi est mort, vive le roi !</strong></div>
						</div>
					</div>
				</div><div id="news_panel-1"><form method="POST" action="/Infinite/Index/update/1" id="form"><label for="form_title">Titre<br /></label><input type="text" name="title" value="Mon super article ;-)" id="form_title" /><br /><label for="form_content">Contenu<br /></label><textarea name="content" id="form_content"><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Untitled document&lt;/title>
&lt;/head>
<body>
<p>CC ^__^&lt;/p>
<p>&nbsp;:3&lt;/p>
<p><img title="Kiss" src="../../static/tiny_mce/plugins/emotions/img/smiley-kiss.gif" alt="Kiss" border="0" />&lt;/p>
&lt;/body>
&lt;/html></textarea><br /><input type="submit" name="send" value="Envoyer" id="form_send" /><input type="hidden" name="sent" value="1" id="form_sent" /></form></div><div id="profil-1" style="display: none;"><b>Pseudo: </b>Vendethiel<br /><span class="f_level" data-id="1"><i>Joueur</i></span><br />Points: <span class="f_points" data-id="1">150000</span><br /><b>Vendethiel</b> est un de vos amis et vous a dans ses amis.<br /><b>Dernière connexion: </b>Le 2011-06-16 01:38:07<br /><a class="link" href="/Infinite/Account/edit/1">Modifier le compte de Vendethiel</a>&nbsp;&bull;&nbsp;<a class="link" href="/Infinite/Account/index/1">Modifier les informations de Vendethiel</a><br /><a class="link" href="/Infinite/Account/show/1">Plus ...</a></div> 
				<div class="post">
					<div class="content">
						<div class="infos">
							<div class="title" id="title1">
								<a class="link" href="/Infinite/News/show/1"><span id="newsTitle">Mon super article ;-)</span></a>  <a class="edit_link_1" href="/Infinite/News/update/1" id="link_6083"><img src="/Infinite/static/templates/default/images/bouton/btn_edit.png" title="" alt="" /></a> <a class="link" href="/Infinite/News/delete/1"><img src="/Infinite/static/templates/default/images/bouton/btn_delete.png" title="" alt="" /></a>
							</div>
							<div class="autre">Par <i><a href="/Infinite/Account/show/1" id="link_7414">Vendethiel</a></i> | Le 2011-06-15 23:50:04. Dernière modification le 2011-07-06 17:51:43. 1 Commentaire</div>
						</div>
						<div align="center" id="cnt1" class="cont">
							&nbsp;&nbsp;CC ^__^<br />&nbsp;:3<br /><img title="Kiss" src="/Infinite/static/tiny_mce/plugins/emotions/img/smiley-kiss.gif" alt="Kiss" border="0" /><br />
						</div>
					</div>
				</div><br /><a class="link" href="/Infinite/Index/update"><img src="/Infinite/static/templates/default/images/bouton/btn_news.png" title="" alt="" /></a> <?php global $router, $connected, $account;
if($c = Cache::start("Account_show_profil_2_" . ($connected ? $account->guid : -1), strtotime("+3 hours")))
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
if($c = Cache::start("Account_show_profil_1_" . ($connected ? $account->guid : -1), strtotime("+3 hours")))
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
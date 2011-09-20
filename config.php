<?php
return array(
# BASICS
#At each installation, you'll edit this part
#Pour chaque installation, vous devrez modifier cette partie
	//Informations on the owner
	//Informations sur les possesseurs du serveurs
	'SERVER_NAME' => 'Myserver',
	'SERVER_EMAIL' => 'contact@monserver.com',
	//you can delete the key below if your server has not corporation
	// (by adding a sharp before the line, #'SERVER_CORP' => ...)
	//vous pouvez supprimer cette traduction si votre serveur n'a pas d'entreprise
	// (en ajoutant un dièse avant la ligne, #'SERVER_CORP' => ...)
	'SERVER_CORP' => 'MyServerCorporation',

	//Title of a page. Vars you can use : {title} (the default title, as "Index")
	// {server.name}, {server.corp}
	//Titre de la page. Variables utilisables : {title} (titre normal, comme "Index")
	// {server.name}, {server.corp}
	'TITLE' => '{page} • {server.name}',

	//Downloads link (Misc/join)
	//Liens de téléchargement (Misc/join)
	'DOWNLOAD' => array(
		//URL for download 1.29.1 client
		//Adresse pour télécharger le client 1.29.1
		'CLIENT' => 'http://www.megaupload.com/?d=DBKDLYTD',
		//URL of the launcher (32 bits)
		//Adresse pour télécharger le launcher (32 bits)
		'LAUNCHER.32' => './client/launcher_setup.exe',
		//URL of the launcher (64 bits)
		//Adresse pour télécharger le launcher (64 bits)
		'LAUNCHER.64' => './client/launcher_setup.exe',
		//URL of the config file
		//Adresse du fichier de configuration
		'CONFIG' => './config.xml',
	),

	//Your RPG Paradize ID (don't put http://rpg-paradize.com/...)
	// IF YOU WANT TO DISABLE VOTE, PUT -1 !
	//Votre ID RPG Paradize (ne mettez pas http://rpg-paradize.com/...)
	// SI VOUS VOULEZ DÉSACTIVER LE VOTE, METTEZ -1 !
	'URL_VOTE' => -1,

	//URL of board (if it's an external url, the url _MUST_ begin with http://)
	// (if you got no board, put # before the line: #'BOARD_URL' => ...)
	//Adresse du forum (si c'est une adresse externe, elle _DOIT_ commencer avec http://)
	// (si vous n'avez pas de forum, ajoutez # au début de la ligne : #'BOARD_URL' => ...)
	'BOARD_URL' => './forum/',

# DB CONFIGURATION
#You won't modify that unless you did some "tricks" on your DB.
#Vous ne devriez pas avoir à modifier ça, sauf si vous avez touché à votre DB
	//TYPE of DB Server (as mysql, mssql)
	//TYPE du serveur BDD (comme mysql/mssql ... J'ai crée ce CMS de manière
	// à ce que ce paramètre n'ai pas de répercussion)
	'DB_TYPE' => 'mysql',
	//HOST of DB Server
	//Hébergeur du serveur BDD
	'DB_HOST' => 'localhost',
	//User of DB Server
	//Utilisateur pour la base de donn&eacute;es
	'DB_USER' => 'root',
	//Password of the user of the DB Server
	//Mot de passe pour se connecter à l'utilisateur dans la base de données
	'DB_PSWD' => '',
	//Database name static
	//Nom de la base de données statique
	'DB_STATIC' => 'ancestra_static',
	//Database name other
	//Nom de la base de données other
	'DB_OTHER' => 'ancestra_other',

# PASS CONFIGURATION
#You must edit that if the shop is enabled
#Vous devez modifier ça si la boutique est activée
	'PASS' => array(
		//Is the pass enabled ?
		//Le système de créditage est-il actif ?
		'enable' => true,

		//different possible type: webo, star (case sensitive)
		//différent types possibles: webo, star (sensible à la casse !)
		'type' => 'webo / star',

		//If it's webopass ('webo' in type)
		//Si c'est webopass ('webo' dans type)
		'cc' => 'ID Webopass CC',
		'document' => 'ID Webopass document',

		//If it's starpass ('star' in type)
		//Si c'est starpass ('star' dans type)
		'idd' => 0,

		//Else: not implemented yet
		//Sinon: pas encore disponible
	),

	//Is the shop enabled ?
	//La boutique est-elle activée ?
	'ENABLE_SHOP' => true,
	//Points given for a vote
	//Points donnés pour un vote
	'POINTS_VOTE' => 1,
	//Points given for one pass
	//Points donnés pour un pass
	'POINTS_CREDIT' => 100,
	//Points given for a vote from a VIP
	//Points donnés pour un vote PAR UN VIP
	'POINTS_VOTE_VIP' => 4,
	//Points given for one pass from a VIP
	//Points donnés pour un pass PAR UN VIP
	'POINTS_CREDIT_VIP' => 140,

	//Used for "server online?"
	//Utilisé pour "serveur en ligne ?"
	'IP_SERV' => 'localhost',
	'PORT_SERV' => 444,

# TEAMSPEAK
	'TEAMSPEAK' => array(
		//Is the TS server is opened?
		//Le serveur TS est-il ouvert ?
		'opened' => true,
		//TS server address
		//Adresse du serveur TS
		'server' => 'mon serveur',
		//Port of the TS Server
		//Port du serveur TS
		'port' => 'mon port',
		//Password, comment this line if your TS serv don't need a pass
		// (by adding a sharp before the line, #'pass' => ...)
		//Mot de passe, commentez cette ligne si votre serveur TS n'en a pas
		// (en ajoutant un dièse avant la ligne, #'pass' => ...)
		'pass' => 'mon mot de passe',
	),

# SETTINGS
#Configuration basics
#Configuration basique
	//Registration enabled ?
	//Inscription activée ?
	'ENABLE_REG' => true,
	//Allow multi-account ?
	//Autoriser le multi-compte ?
	'ALLOW_MULTI' => true,

	//Number of bugs by pages on the BugTracker
	//Nombre de bugs à montrer par page sur le bugTracker
#	'BUGS_BY_PAGE' => 30, //commented: not finished functionnality | commenté: fonctionnalité non terminée
	//Number of articles by pages on the index
	//Nombre d'articles à montrer par page sur l'index
	'ARTICLES_BY_PAGE' => 3,
	//Number max of comments on article view (-1 for all / 0 to disable)
	//Nombre maximum de commentaire à afficher sur la vu d'un article (-1 pour tous / 0 pour désactiver)
	'MAX_COMMENTS' => 3,
	//Number of rate by page in the GuestBook.
	// To disable the GuestBook, put -1
	//Nombre de commentaires par page dans le Livre d'Or
	// Pour désactiver le livre d'or, mettez -1
	'RATES_BY_PAGE' => 5,
	//Number of Private Messages by page to show (in private message index)
	//Nombre de messages privés à montrer (dans la boîte de réception)
	'PM_BY_PAGE' => 10,
	//Number of Private Message Answers by page to show (in private message view)
	//Nombre de messages aux messages privés à montrer (dans la vue d'un message privé)
	'PMA_BY_PAGE' => 3,
	//Number of items to show by line
	//Nombre d'objets par ligne dans la boutique
	'ITEMS_BY_LINE' => 2,
	//Number of lines by page on shop
	//Nombre de lignes d'objets par page dans la boutique
	'ITEM_LINES_BY_PAGE' => 2,
	//Number of character by page on ladder
	//Nombre de personnages par page dans les ladders
	'LADDER_LIMIT' => 15,
	//Show admins on ladder ?
	//Afficher les admins sur le ladder ?
	'LADDER_ADMIN' => false,

	//STATS: Show (or not) "Accounts created", "Characters created" and "accounts logged on"
	// It costs 3 SQL Queries (1h cache). It enables also the Misc/stats page (showing all stats, 6h cache)
	//STATS: Montrer (ou pas) "Comptes crées", "Personnages crées" et "connectés"
	// Cela coûte 3 requêtes, il y a un cache d'une heure. Cela active aussi la page Misc/stats (toutes les stats, cache de 6h)
	'STATS' => true,

# ADVANCED CONFIG
	//default lang
	//langue de base
	'use_lang' => 'fr',
	//Langs to load (if commented, it load only "use_lang")
	// to uncomment, remove the sharp at the beginning of the line
	//Langues à charger (si commenté, la seule langue chargée sera celle d'use_lang)
	// Pour décommenter, enlever le dièse avant la ligne
	//!\Format/!\: 'langs' => array( '1', '2', '3', '4', '5' ),
#	'langs' => array( 'fr', 'en' ),

	//Template to use (default => 'default')
	//Thème à utiliser (par défaut => 'default')
	'template' => 'default',


	//Use rewrite_mod ? (/News/show/[id] instead of ?controller=news&action=show&id=[id])
	// If you get a 404 (not found) error with true, put this instead (or change your host).
	//Utiliser la réecriture d'adresse ? (/News/show/[id] à la place de
	//  ?controller=news&action=show&id=[id])
	// Si vous avez une erreur 404 (not found) avec true, passez à false (ou changez d'hébergeur).
	'REWRITE' => true,

	//Type of AJaX-Load (no-refresh)
	//Type de chargement AJaX (pas de rechargement)
	/*
	 * LOAD_NOTHING => nothing
	 *			rien
	 * LOAD_CONTENT => page content changed with loading message
	 *			contenu de la page remplacé avec le message de chargement
	 * LOAD_MDIALOG => modal dialog box with loading message
	 *			Fenêtre avec le message de chargement
	 * LOAD_NONE	=> no AJaX (by default if JAVASCRIPT => false)
	 *			désactive l'AJaX (par défaut si JAVASCRIPT => false)
	 */
	'LOAD_TYPE' => LOAD_MDIALOG,

	//Enable the JavaScript (widgets: modal box, accordeon, AJaX, Edit In Place, ...), it forces LOAD_TYPE to be NONE
	//Activer le JavaScript (modules: boîte de dialogues, accordeon, AJaX, édition rapide, ...) force LOAD_TYPE à NONE
	'JAVASCRIPT' => true,
);
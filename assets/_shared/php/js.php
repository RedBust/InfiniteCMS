<?php
if (level(LEVEL_ADMIN))
{
	$js = '';
	$level_opt = array();
	$levels = Member::getLevels();
	unset($levels[LEVEL_GUEST]);
	foreach (array_reverse($levels, true) as $val => $text)
		$level_opt[] = '"' . floatval($val) . '": "' . $text . '"';
	$level_opt = implode(', ', $level_opt);
	jQ('var level_opts = {' . $level_opt . '};');
	
	$spec = array('level' => '
	field_type: "select",
	select_text: $this.html(),
	select_options: level_opts,');
	$types = array('points', 'level');

	$controllers = array('points' => 'User', 'level' => 'Account');
	foreach ($types as $type)
		$js .= '
	var data_id;
	function apply_' . $type . '()
	{
		//cache is not usable =x
		jQuery(".f_' . $type . '").each(function()
		{
			$this = jQuery(this);
			if(!$this.attr("validated"))
			{
				$this.editInPlace(
				{
					url: "' . to_url(array(
						'controller' => $controllers[$type],
						'action' => 'update',
						'header' => 0,
						'col' => $type,
						'id' => '%%$this.data(\'id\')%%',
					), false) . '",
					success: function (newValue)
					{
						$(".f_' . $type . '[data-id=" + $this.data("id") + "]").each(function()
						{
							$(this).html(newValue);
						});
					},
					' . ( isset($spec[$type]) ? $spec[$type] : '' ) . '
				});
				$this.attr("validated", true);
			}
		});
		setTimeout(apply_' . $type . ', 5000);
	}
	apply_' . $type . '();';
	jQ($js);
}
?>
<script type="text/javascript">
var dialogOpt =
{
	autoOpen: false,
	draggable: true,
	modal: true,
	resizable: true,
	width: 600
};
var dialogOptO = //O = open
{
	autoOpen: true,
	draggable: true,
	modal: true,
	resizable: true,
	width: 600
};
var path = '<?php echo getPath() ?>';
var tinyMCEOpt =
{
	script_url : path + 'assets/_shared/js/TinyMCE/tiny_mce.js',

	// General options
	theme : 'advanced',
	plugins : 'safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,iespell,spellchecker,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,fullpage,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups', //'fullpage,save',
	theme_advanced_buttons3_add : 'fullpage',

	//templates
	skin : 'o2k7',
	skin_variant : 'silver',

	//themes opts
	theme_advanced_buttons1 : 'save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect',
	theme_advanced_buttons2 : 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor',
	theme_advanced_buttons3 : 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen',
	theme_advanced_buttons4 : 'insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak', //,blockquote,|,insertfile,insertimage',
	theme_advanced_toolbar_location : 'top',
	theme_advanced_toolbar_align : 'left',
	theme_advanced_statusbar_location : 'bottom',
	theme_advanced_resizing : true

	// Drop lists for link/image/media/template dialogs
	/*
	template_external_list_url : 'assets/lists/template_list.js',
	external_link_list_url : 'assets/lists/link_list.js',
	external_image_list_url : 'assets/lists/image_list.js',
	media_external_list_url : 'assets/lists/media_list.js'
	*/
};
var csrf_token = '<?php echo session_id() ?>';



function explode(delimiter, string, limit)
{	//(thanks to phpJS for this function)
	//Why I use this, and not the native String.split ? because 'limit' param is not delimiting when stop,
	// but what to NOT return ...
	var emptyArray = { 0: '' };

	// third argument is not required
	if( arguments.length < 2 ||
		typeof arguments[0] == undefined || typeof arguments[1] == undefined )
	{
		return null;
	}

	if( delimiter === '' || delimiter === false || delimiter === null )
	{
		return false;
	}
	if( typeof delimiter == 'function'
		|| typeof delimiter == 'object'
		|| typeof string == 'function'
		|| typeof string == 'object' )
	{
		return emptyArray;
	}

	if( delimiter === true )
	{
		delimiter = '1';
	}
	if( !limit )
	{
		return string.toString().split( delimiter.toString() );
	}
	else
	{
		// support for limit argument
		var splitted = string.toString().split( delimiter.toString() );
		var partA = splitted.splice( 0, limit - 1 );
		var partB = splitted.join( delimiter.toString() );
		partA.push( partB );
		return partA;
	}
}
function str_replace (search, replace, subject, count) {
	var i = 0, j = 0, temp = '', repl = '', sl = 0, fl = 0,
	f = [].concat(search),
	r = [].concat(replace),
	s = subject,
	ra = r instanceof Array, sa = s instanceof Array;    s = [].concat(s);
	if (count) {
		this.window[count] = 0;
	}
	for (i=0, sl=s.length; i < sl; i++) {
		if (s[i] === '') {
			continue;
		}
		for (j=0, fl=f.length; j < fl; j++) {            temp = s[i]+'';
			repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
			s[i] = (temp).split(f[j]).join(repl);
			if (count && s[i] !== temp) {
				this.window[count] += (temp.length-s[i].length)/f[j].length;}        }
	}
	return sa ? s : s[0];
}
</script>
<?php echo javascript_tag() ?>
<script type="text/javascript">
function resetMarks()
{
	$('.hideThis').remove();
	jQuery('.showThis').show();
}

var locations = new Array();
pageBinds =
{
	base_ajax_binds:
	{
		'before': [],
		'after': [ resetMarks ]
	},
	ajax_binds: {},

	<?php if ($config['LOAD_TYPE'] != LOAD_NONE): ?>
	reset: function ()
	{
		this.ajax_binds = this.base_ajax_binds;
	},
	add: function (fn, pos)
	{
		if (!pos || pos == 'before')
			this.ajax_binds['before'].push( fn );
		else
			this.ajax_binds[pos].push( fn );
	},
	process: function (pos)
	{
		jQuery.each( this.ajax_binds[pos], function (k, v)
		{
			if (jQuery.isFunction(v))
				v();
			else
				eval(v + '()');
		} );
		this.ajax_binds[pos] = this.base_ajax_binds[pos];
	}
	<?php else: ?>
	reset: function () {}
	add: function () {}
	process: function () {}
	<?php endif ?>
};
function pageBind(fn, pos)
{
	pageBinds.add(fn, pos);
}
pageBinds.reset();
<?php if ($config['LOAD_TYPE'] == LOAD_MDIALOG): ?>
var loader = $('#loading');
loader.dialog( dialogOpt );
<?php endif ?>
var errorDiv = $('#errorDiv'),
	error = $('#error'),
	updateSelectorsDisabled = [];
errorDiv.dialog(dialogOpt);

<?php if (level(LEVEL_LOGGED)): ?>
function chooseMainChar(character)
{ //not using followLink to refresh mainChar panel
	document.location = "<?php echo to_url(array(
				'controller' => 'User',
				'action' => 'main',
				'id' => ''
			)) ?>" + character;
}
var mainCharSelector = $('#selectMainChar').dialog(dialogOpt);
$('#charactersList').accordion({clearStyle: true, collapsible: true, active: false});
<?php if (defined('IN_PM') && IN_PM): ?>
//THIS IS CRAPPY @TO DO REMOVE THAT
updateSelectorsDisabled.push('#pm');
<?php else: ?>
$("#pm").dialog(dialogOpt);
<?php endif ?>

<?php endif ?>

function isLocalURI(href)
{
	return href.substr(0, 1) == '?'
		|| href == './'
		|| href.substr(0, 1) == '/';
}
var href,
	url = '<?php echo $_SERVER['REQUEST_URI'] ?>',
	in_ajax = false,
	popState = false,

	servInfo = $('#servInfo'),
	milieu = $('#milieu'),
	pm_info = $('#pm_info'),
	pm_inbox = $('#pm_inbox')

	inbox_html = '&nbsp;<?php echo make_link('@pm', make_img('icons/email', EXT_PNG, lang('PrivateMessage - index', 'title')), array(), array('data-unless-selector' => '#pm')) ?>';

function populateContent(data)
{
	binds.process('before');
	<?php if ($config['LOAD_TYPE'] === LOAD_MDIALOG): ?>
	loader.dialog('close');
	<?php endif ?>
	//we need path because of URL rewriting
	//@todo: check if the path which have to be used is not the one from the 1st page loaded
	servInfo.css('background', 'url(' + data[1] + 'assets/<?php echo $config['template'] ?>/images/status' + data[2] + '.<?php echo EXT_JPG ?>');
	pm_info.html(data[3]);
	if (data[3] == '') //no mp
		pm_inbox.html(inbox_html);
	else
		pm_inbox.html('');

	if (data[4] != '')
	{
		$.each(updateSelectorsDisabled, function (k, v)
		{
			if (v == data[4])
			{
				data[4] = '';
				return false;
			}
		});
	}
	if (data[4] != '')
	{ //may have changed
		var updateElement = $(data[4]);
		if (updateElement.length)
			updateElement.html(data[5]);
		else
			data[4] = '';
	}
	if (data[4] == '')
	{
		milieu.html(data[5]);
		document.title = data[0];
	}
	in_ajax = false;
	binds.process('after');
	binds.reset();
}
function updateContent(URL)
{
	if( in_ajax )
		return;
	in_ajax = true;
	<?php if ($config['LOAD_TYPE'] === LOAD_MDIALOG): ?>
	loader.dialog('open');
	<?php elseif ($config['LOAD_TYPE'] === LOAD_CONTENT): ?>
	milieu.html(<?php echo javascript_val(lang('loading')) ?> + ' ...');
	<?php endif ?>
	$.ajax(
	{
		url: URL,
		success: function (data)
		{
			//data = Title<~>Path<~>Status<~>PM Info<~>update selector<~>DOM
			data = explode('<~>', data, 6);
			populateContent(data);
			if (popState)
			{
				popState = false;
			}
			else if (data[4] == '' && history && history.pushState)
			{
				history.pushState(true, data[0], URL);
			}
		},
		error: function (error)
		{
			loader.html('An error occured during the page loading. Try to change the "REWRITE" setting in your config.php file.');
			<?php if (DEBUG): ?>
			loader.html( loader.html() + error );
			<?php endif ?>
			in_ajax = false;
		}
	} );
}
<?php if ($config['LOAD_TYPE'] == LOAD_NONE): ?>
function followLink(link)
{
	document.location = link;
}
<?php else: ?>
function followLink(event)
{
	var $this = $(this);

	if (typeof event == 'string')
		href = event;
	else
		href = $this.attr('href');
	if (url === href && <?php echo javascript_val(!DEBUG) ?>) //re-load same page ? useless ...
	{
		if (typeof event != 'string')
			event.preventDefault();
		return false;
	}
	else
		url = href;

	if ($this.data('unless-selector') !== undefined)
	{ //this behavior is a little bit special.
	 // maybe I should load the URL if !load (first one) OR unlessSelector.url == url
	 // that would reload it ONLY if we're not doing anything else than watching the link's page
	 // or add an attribute, as data-reload-when="same-page" (i.e.)
		var unlessSelector = $($this.data("unless-selector"));
		if (unlessSelector.length && $.trim(unlessSelector.html()) != '')
		{
			if (unlessSelector.is(':visible'))
				return false;

			if (unlessSelector.hasClass('ui-dialog-content'))
				unlessSelector.dialog('open');
			else
				unlessSelector.show();

			return false;
		}
	}
	if (isLocalURI(href))
	{
		updateContent(href);
		if (typeof event != 'string')
			event.preventDefault();
		return false;
	}
	else
	{
		document.location = href;
	}
};
$('.link').live('click', followLink);

var _inLoad = false;
$(window).bind('popstate', function ()
{
	if (_inLoad)
	{
		_inLoad = false;
		return;
	}

	popState = true;
	updateContent(location.href);
}).bind('load', function ()
{
	_inLoad = true;
});
<?php endif ?>
var lang = {};
lang['FORM.MUST_SPECIFY_ps'] = <?php echo javascript_val(lang('acc.login.spec_login')) ?>;
lang['FORM.MUST_SPECIFY_pa'] = <?php echo javascript_val(lang('acc.login.spec_pass')) ?>;
var LoginForm_force = false, //force to submit ?
	LoginForm_processing = false, //in AJaX request

	form_login = $('#login_form'),
	field_pseudo = form_login.find('#form_<?php echo Member::CHAMP_PSEUDO ?>'),
	field_pass = form_login.find('#form_<?php echo Member::CHAMP_PASS ?>');
/*var last_pseudo = '', last_pass = '';*/
form_login.submit(function(event)
{
	if (LoginForm_force)
		return;
	if (LoginForm_processing)
	{
		event.preventDefault();
		return false;
	}

	var cont = true,
		msg = '';
	if (field_pseudo.val() == '')
	{
		cont = false;
		msg += lang['FORM.MUST_SPECIFY_ps'] + '<br />';
	}
	if (field_pass.val() == '')
	{
		cont = false;
		msg += lang['FORM.MUST_SPECIFY_pa'];
	}
	if (!cont)
	{
		error.html(msg);
		errorDiv.dialog('open');
		event.preventDefault();
		return false;
	}

	/*if (field_pseudo.val() == last_pseudo && field_pass.val() == last_pass)
		return false;
	last_pseudo = field_pseudo.val();
	last_pass = field_pass.val();*/

	var errorMessages = {
		'bad': <?php echo javascript_val(lang('acc.invalid_login_action')) ?>,
		'ban': <?php echo javascript_val(lang('acc.banned')) ?>
	},
	t = $(this),
	pseudo = encodeURIComponent(field_pseudo.val()),
	pass = encodeURIComponent(field_pass.val());
	LoginForm_processing = true;
	$.ajax(
	{
		mode: 'POST',
		url: <?php echo '"' . to_url(array(
			'controller' => 'User',
			'action' => 'login',
			'check' => '1',
			Member::CHAMP_PSEUDO => '%%pseudo%%',
			Member::CHAMP_PASS => '%%pass%%',
		), false) . '"' /* javascript_val escapes " :p */ ?>,
		success: function (status)
		{
			LoginForm_processing = false; //process ended
			if (status == 'ok')
			{ //status is ok, we just submit the form :)
				LoginForm_force = true;
				t.submit();
			}
			else
			{
				if (errorMessages[status])
					error.html(errorMessages[status]);
				else
					error.html(status); //something is not going on perfectly ...

				errorDiv.dialog('open');
				event.preventDefault();
				return false;
			}
		},
		error: function ()
		{
			LoginForm_processing = false;
			alert('error: unable to join login page');
		}
	} );
	event.preventDefault();
	/*
	//slower but usefull for form with changing fields-id
	// and this version requires all fields in MUST_SPECIFIY_form_[field ID]
	var inputs = $( this ).find(':input');
	var input;
	$.each( inputs, function(key,value)
	{
		input = $( value );
		if( input.val() == '' )
		{
			error.html( lang['FORM.MUST_SPECIFY_' + input.attr('id')] );
			errorDiv.dialog('open');
			event.preventDefault();
		}
	} );
	//*/
} );

var t;
$('.slideMenu').live('click', function ()
{
	$this = $(this);
	$this.next().slideToggle();
	if ($this.hasClass('hideThis'))
		$this.slideToggle();
} );
resetMarks();


<?php echo jQ() ?>
</script>
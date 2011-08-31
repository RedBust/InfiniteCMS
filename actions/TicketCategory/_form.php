<?php
$icon_path = sprintf('static/templates/%s/images/', $config['template']);
echo make_form(array(
	array('name', lang('name') . tag('br'), NULL, $category->name),
	array('desc', lang('desc') . tag('br'), NULL, $category->description),
	array('icon', lang('icon') . sprintf(lang('icon_tips'), $config['template']) . tag('br') .
	 tag('b', lang('actual') . ':') . tag('img', array('id' => 'icon_preview', 'src' => $icon_path . $category->icon . '.png' )) . tag('br'), NULL, $category->icon),
));
jQ('var icon_preview = $("#icon_preview");
$("#form_icon").change(function ()
	{
		icon_preview.attr("src", "' . $icon_path .  '" + $(this).val() + ".png");
	});');
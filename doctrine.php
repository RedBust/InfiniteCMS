#!usr/bin/php php
<?php
//Nami-D0C: Yeah, I know, this file is not so clean ...
// But you must *NOT* upload it to your server
defined('ROOT') || define('ROOT', './');
define('EXT', strrchr(__FILE__, '.'));
define('DEV', true); //we're in dev. mode
define('DEBUG', false);

$hasArg = isset( $_SERVER['argv'][1] );
if (!$hasArg || ( $hasArg && $_SERVER['argv'][1] == '--generate-models' ) )
{
	require 'lib/bootstrap' . EXT;

	$options = array(
		'generateTableClasses' => true,
		'phpDocPackage' => 'InfiniteCMS',
		'phpDocSubpackage' => 'Models',
		'phpDocName' => 'Vendethiel',
		'phpDocEmail' => 'vendethiel@hotmail.fr',
		'baseClassName' => 'Record',
		'baseTableClassName' => 'RecordTable',
	);

	$bdir = 'lib/models/other/';
	Doctrine_Core::generateModelsFromYaml($bdir . 'yaml', $bdir . 'php', $options);
	load_models('other');
	$file = fopen($bdir . 'sql/schema.sql', 'w+');
	fwrite($file, Doctrine_Core::generateSqlFromModels($bdir . 'php/'));
	fclose($file);
#	Doctrine_Core::createTablesFromModels($bdir . 'php/');
	echo 'generated !';
}
else
{
	$cli = new Doctrine_Cli(array(
		'models_path'         =>	ROOT . 'lib/models/other/php',
		'yaml_schema_path'    =>	'lib/models/other/yaml'
	));
	$cli->run( $_SERVER['argv'] );
}
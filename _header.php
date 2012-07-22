<?php
	//Kludge to be able to load libraries both in Pluton and in stand-alone tool version.
	$tool_directory = getcwd() . '/tools/SourceTemplatesGenerator';
	ini_set('include_path', ini_get('include_path') . ":$tool_directory");
?>

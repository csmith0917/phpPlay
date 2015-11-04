<?php 

	$title = basename($_SERVER['SCRIPT_FILENAME'], '.php');
	//$title = ucfirst($title);
	//$title = ucfirst(basename($_SERVER['SCRIPT_FILENAME'], '.php'));
	$title = str_replace('_', ' ', $title);
	$title = ucwords($title);
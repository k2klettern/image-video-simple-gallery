<?php
/*
Plugin Name: Image Video Simple Gallery
Description: A simple Image or Video Gallery Creator
Version: 0.1
Author: Eric Zeidan
*/


defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

foreach (glob(__DIR__ . "/classes/class-*.php") as $filename)
include_once $filename;

define('IVS_BASE_DIR', plugin_dir_path(__FILE__));
define('IVS_BASE_URL', plugin_dir_url(__FILE__));
define('IVS_BASENAME', plugin_basename(__FILE__));
define('IVS_TEXT_DOMAIN', 'ivs_plugin');

/**
* We create the instance
*/
$ivs = new ivsPlugin();

/**
* Functions for redirect on activation and include action on activation of plugin
*/
register_activation_hook(__FILE__, array($ivs, "ivsActivate"));
<?php

set_include_path(__DIR__ . '/..');
error_reporting(E_ALL | E_STRICT);

// Get includes
require_once('vendor/autoload.php');
require_once('src/config.php');
require_once('src/models/database.php');
require_once('src/models/twitter.php');
require_once('src/logs/log.php');
require_once('src/errors/error.php');
require_once('src/helpers/includes.php');
require_once('src/actions/save-new-objects.php');

// Setup vars, config and database
$config = new SocialHelper\Config\Config();
$vars = $config->getVars();
$config = $config->getConfig();
$db = new SocialHelper\Database\Database($config);
$twitter_app_connection = new SocialHelper\Twitter\Twitter($config);
$twitter_connections = array('app' => $twitter_app_connection);

// Run actions
save_new_objects();

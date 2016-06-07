<?php

set_include_path(__DIR__ . '/..');
error_reporting(E_ALL | E_STRICT);

// Get includes
require_once('src/config.php');
require_once('src/models/database.php');
require_once('src/logs/log.php');
require_once('src/errors/error.php');
require_once('src/helpers/validations/includes.php');
require_once('src/helpers/tracking-query.php');
require_once('src/helpers/get-tracking-queries.php');
require_once('src/helpers/get-objects-by-tracking-query.php');
require_once('src/actions/save-new-objects.php');

// Setup vars, config and database
$config = new SocialHelper\Config\Config();
$vars = $config->getVars();
$config = $config->getConfig();
$db = new SocialHelper\Database\Database($config);

// Run actions
save_new_objects();

<?php

/**
 * Plugin Name: Bet Sport Free
 * Description: Plugin to show matches with leagues and allow user to bet 
 * Version:     1.0.0
 * Author:      Ryan
 * Author URI:  https://www.fiverr.com/ryanvalenzuela
 */


namespace BetPlugin;

use BetPlugin\Inc\BetPlugin;
use BetPlugin\Inc\Options;
use BetPlugin\Shortcodes\Shortcodes;

define('BETPLUGIN_PLUGIN_URL', plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__)));
define('BETPLUGIN_ASSETS_URL', BETPLUGIN_PLUGIN_URL . '/assets/');
define('BETPLUGIN_VERSION', '1.0.0');

require __DIR__ . '/autoloader.php';

new BetPlugin();
new Options();
new Shortcodes();

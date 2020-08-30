<?php
/*
 * Plugin Name: Multiple Lines VOD
 * Description: Play Videos On Demand with Multiple Lines
 * Plugin URI:        https://plugins.top/plugins/ml-vod/
 * Version: 0.5
 * Author:            edear
 * Author URI:        https://plugins.top/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ml-vod
 * Domain Path:       /languages
 */
 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
define( 'VERSION_MLVOD_PLUGIN',  '1.0');
define( 'PATH_MLVOD_PLUGIN',  plugin_dir_path( __FILE__ ));
define( 'BASENAME_MLVOD_PLUGIN',  plugin_basename(__FILE__));
define( 'URL_MLVOD_PLUGIN',  plugins_url('', __FILE__));

require_once(PATH_MLVOD_PLUGIN . 'class/frontend.php');
require_once(PATH_MLVOD_PLUGIN . 'class/admin.php');
add_action( 'plugins_loaded', array( 'MLVOD_Frontend_Class', 'init' ) );
add_action( 'plugins_loaded', array( 'MLVOD_Admin_Class', 'init' ) );
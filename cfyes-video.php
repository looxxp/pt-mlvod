<?php
/*
Plugin Name: CFyes video
Description: CFyes Multiple lines video
Version: 1.0
 */
 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
define( 'VERSION_CFYES_VIDEO_PLUGIN',  '1.0');
define( 'PATH_CFYES_VIDEO_PLUGIN',  plugin_dir_path( __FILE__ ));
define( 'URL_CFYES_VIDEO_PLUGIN',  plugins_url('', __FILE__));

require_once(PATH_CFYES_VIDEO_PLUGIN . 'class/main.php');
add_action( 'plugins_loaded', array( 'cfyes_video_main_class', 'init' ) );
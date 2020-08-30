<?php

class MLVOD_Admin_Class{
	
	private static $initiated = false;
	private static $default_template = '<div class="embed-responsive embed-responsive-16by9">
<video id="%1$s" poster="" poster="%2$s" class="video-js vjs-16-9 vjs-theme-fantasy cfyes-video" controls="" data-setup="{}"  autoplay>
<source src="%3$s" type="application/x-mpegURL">
</video>
</div>
<ul class="lines">%4$s</ul>';
	
	/**
	* Init
	*/
	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}
	
	/**
	* add actions/filters
	*/
	public static function init_hooks() {
		self::$initiated = true;
		add_action( 'admin_menu', array( 'MLVOD_Admin_Class', 'admin_menu' ) );
		add_action( 'admin_init', array( 'MLVOD_Admin_Class', 'admin_init' ) );
		add_filter( 'plugin_action_links_' . BASENAME_MLVOD_PLUGIN, array( 'MLVOD_Admin_Class', 'add_plugin_page_settings_link'));
	}
	
 
    /**
     * Registers a new settings page under Settings.
     */
    public static function admin_menu() {
        add_options_page(
            __( 'MLVOD Options', 'mlvod' ),
            __( 'MLVOD', 'mlvod' ),
            'manage_options',
            'options_mlvod',
            array(
                'MLVOD_Admin_Class',
                'settings_page'
            )
        );
    }
	
    /**
     * Settings page display callback.
     */
    public static function settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( isset( $_GET['settings-updated'] ) ) {
			// add settings saved message with the class of "updated"
			add_settings_error( 'mlvod_messages', 'mlvod_messages', __( 'Settings Saved', 'mlvod' ), 'updated' );
		}
	?>
	<form action="options.php" method="post">
	<?php
		settings_fields( 'options_mlvod' );
		do_settings_sections( 'options_mlvod' );
		submit_button( __( 'Save Settings', 'mlvod' ) );
	?>
	</form>
	<?php
    }
	
    /**
     * Settings page display callback.
     */
    public static function admin_init() {
		register_setting( 'options_mlvod', 'mlvod-load-videojs', array(
			'type' => 'boolean', 
			'sanitize_callback' => 'intval',
			'default' => 1,
		) );
		register_setting( 'options_mlvod', 'mlvod-lines-json', array(
			'type' => 'string', 
			'sanitize_callback' => 'esc_textarea',
			'default' => '',
		) );
		register_setting( 'options_mlvod', 'mlvod-template', array(
			'type' => 'string', 
			'sanitize_callback' => 'balanceTags',
			'default' => self::$default_template,
		) );
        add_settings_section(
            'mlvod_setting_section',
            __( 'MLVOD Setting', 'mlvod' ),
            array('MLVOD_Admin_Class','mlvod_setting_section_cb'),
            'options_mlvod'
        );
		add_settings_field(
			'mlvod-load-videojs', 
			__( 'Load VideoJS CSS/JS files', 'mlvod' ),
			array('MLVOD_Admin_Class', 'callback_load_videojS'),
			'options_mlvod',
			'mlvod_setting_section', 
			[
				'label_for' => 'load-videojs'
			] 
		);
		add_settings_field(
			'mlvod-lines-json', 
			__( 'Lines JSON Setting', 'mlvod' ),
			array('MLVOD_Admin_Class', 'callback_lines_json'),
			'options_mlvod',
			'mlvod_setting_section', 
			[
				'label_for' => 'lines-json'
			] 
		);
		add_settings_field(
			'mlvod-template', 
			__( 'Video Player Template', 'mlvod' ),
			array('MLVOD_Admin_Class', 'callback_template'),
			'options_mlvod',
			'mlvod_setting_section', 
			[
				'label_for' => 'mlvod-template'
			] 
		);
	}
	
	public static function callback_load_videojS() {
		$format = '<input name="mlvod-load-videojs" type="checkbox" id="mlvod-load-videojs" value="1" %1$s>';
		echo  sprintf($format, get_option('mlvod-load-videojs')?'checked=""':'');
	}
	
	public static function callback_lines_json() {
		$format = '<textarea name="mlvod-lines-json" rows="10" cols="50" id="mlvod-lines-json" class="large-text code">%1$s</textarea>';
		echo  sprintf($format, get_option('mlvod-lines-json'));
	}
	
	public static function callback_template() {
		$format = '<textarea name="mlvod-template" rows="10" cols="50" id="mlvod-template" class="large-text code">%1$s</textarea>';
		echo  sprintf($format, get_option('mlvod-template'));
	}
	
	public static function mlvod_setting_section_cb() {
		//do_settings_fields( 'options_mlvod', 'mlvod_setting_section' );
	}
	
	public static function add_plugin_page_settings_link($links) {
		$links[] = '<a href="' .
			admin_url( 'options-general.php?page=options_mlvod' ) .
			'">' . __('Settings') . '</a>';
		return $links;
	}
}

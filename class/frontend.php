<?php

class MLVOD_Frontend_Class{
	
	private static $initiated = false;
	
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
		add_shortcode( 'mlvod', array('MLVOD_Frontend_Class', 'video_sc') );
		add_filter( 'mlvod_filter_lines', array('MLVOD_Frontend_Class', 'parse_lines'), 10, 1 );
	}
	
	public static function video_sc($atts, $content) {
		wp_enqueue_script( 'videojs-js', 'https://cdn.jsdelivr.net/npm/video.js@7.8.4/dist/video.min.js', null, 'v7.8.4', true );
		wp_enqueue_style( 'videojs-css', 'https://cdn.jsdelivr.net/npm/video.js@7.8.4/dist/video-js.min.css', array(), 'v7.8.4', 'all' );
		wp_enqueue_style( 'videojs-fantasy-css', 'https://cdn.jsdelivr.net/npm/@videojs/themes@1.0.0/fantasy/index.css', array(), 'v1.0.0', 'all' );
		wp_enqueue_script( 'mlvod-js', URL_MLVOD_PLUGIN . '/assets/frontend.js', null, VERSION_MLVOD_PLUGIN, true );
		wp_enqueue_style( 'mlvod-css', URL_MLVOD_PLUGIN . '/assets/style.css', null, VERSION_MLVOD_PLUGIN, 'all' );
		
		$atts = shortcode_atts( array(
			'm3u8' => '',
			'poster'=>'',
		), $atts);
		
		$player_id = apply_filters('mlvod_player_id', 'mlvod-' . self::getSubstringFromEnd(wp_hash(rand(10000, 20000)), 6));
		$template = apply_filters('mlvod_tp', get_option('mlvod-template'));
		$lines = apply_filters('mlvod_filter_lines', get_option('mlvod-lines-json'));
		$line0 = '';
		if(isset($lines[0][1])){
			$line0 = $lines[0][1];
		}
		$m3u8 = apply_filters('mlvod_m3u8', self::parse_m3u8($atts['m3u8'], $line0));
		$lines_html = apply_filters('mlvod_lines_html', self::build_lines_html($lines, get_option('mlvod-line_format'), $m3u8, $player_id));
		$res = sprintf($template, $player_id, filter_var($atts['poster'], FILTER_VALIDATE_URL), $m3u8, $lines_html);
		$res = apply_filters('mlvod_player_html', $res);
		return $res;
	}
	
	public static function parse_lines($json = ''){
		$res = array();
		if(self::isJson($json)){
			$res = json_decode($json, true);
		}
		return $res;
	}
	
	public static function get_template($id = 0){
		$res = '';
		if($id){
			$res = get_the_content(null, false, $id);
		}
		return $res;
	}
	
	public static function parse_m3u8($url = '', $line){
		if (filter_var($url, FILTER_VALIDATE_URL) && $line) {
			$arr = parse_url($url);
			$url = str_replace($arr['host'], $line, $url);
		}
		return $url;
	}
	
	public static function build_lines_html($lines=array(), $format='', $m3u8, $player_id){
		$res = '';
		foreach($lines as $v){
			if(isset($v[0], $v[1])){
				$res .= sprintf($format, self::parse_m3u8($m3u8, $v[1]), $player_id, $v[0]);
			}
		}
		return $res;
	}
	
	public static function getSubstringFromEnd(string $string, int $length)
	{
		return substr($string, strlen($string) - $length, $length);
	}
	
	public static function isJson(string $string)
	{
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}
}

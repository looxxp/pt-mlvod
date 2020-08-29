<?php

class CFyes_Video_Main_Class{
	
	private static $initiated = false;
	private static $line_format = '<li class="cv-line"><a href="#" class="cv-line-link" line="%1$s" vp="%2$s">%3$s</a></li>';
	
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
		add_shortcode( 'CFyes-Video', array('CFyes_Video_Main_Class', 'cfyes_video_sc') );
		add_filter( 'cfyes_video_filter_lines', array('CFyes_Video_Main_Class', 'filter_lines'), 10, 1 );
	}
	
	/**
	* Plugin activated, add CRON job
	*/
	public static function cfyes_video_sc($atts, $content) {
		wp_enqueue_script( 'video-js', 'https://cdn.jsdelivr.net/npm/video.js@7.8.4/dist/video.min.js', null, 'v7.8.4', true );
		wp_enqueue_style( 'video-css', 'https://cdn.jsdelivr.net/npm/video.js@7.8.4/dist/video-js.min.css', array(), 'v7.8.4', 'all' );
		wp_enqueue_style( 'video-sea-css', 'https://cdn.jsdelivr.net/npm/@videojs/themes@1.0.0/fantasy/index.css', array(), 'v1.0.0', 'all' );
		wp_enqueue_script( 'cfyes-video-js', URL_CFYES_VIDEO_PLUGIN . '/assets/cfyes-video.js', null, VERSION_CFYES_VIDEO_PLUGIN, true );
		wp_enqueue_style( 'cfyes-video-css', URL_CFYES_VIDEO_PLUGIN . '/assets/cfyes-video.css', null, VERSION_CFYES_VIDEO_PLUGIN, 'all' );
		
		$atts = shortcode_atts( array(
			'm3u8' => '',
			'lines' => '',
			'tp' => 0,
			'poster'=>'',
		), $atts);
		
		$lines = apply_filters('cfyes_video_filter_lines', $atts['lines']);
		$player_id = 'cv-' . self::getSubstringFromEnd(wp_hash(rand(10000, 20000)), 6);
		$lines = apply_filters('cfyes_video_lines', self::parse_lines($lines));
		$template = apply_filters('cfyes_video_tp', self::get_template($atts['tp']));
		$m3u8 = apply_filters('cfyes_video_m3u8', self::parse_m3u8($atts['m3u8'], $lines[0][1]));
		$lines_html = apply_filters('cfyes_video_lines_html', self::build_lines_html($lines, self::$line_format, $m3u8, $player_id));
		$res = sprintf($template, $player_id, filter_var($atts['poster'], FILTER_VALIDATE_URL), $m3u8, $lines_html);
		return $res;
	}
	
	public static function parse_lines($str = ''){
		$res = array();
		$arr = explode('|', $str);
		foreach($arr as $v){
			$arr2 = explode(':', $v);
			$res[] = $arr2;
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
	
	public static function parse_m3u8($url = '', $lines0 = ''){
		if (filter_var($url, FILTER_VALIDATE_URL) && $lines0) {
			$arr = parse_url($url);
			$url = str_replace($arr['host'], $lines0, $url);
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
	
	public static function filter_lines(string $lines)
	{
		$res = $lines;
		if( !$res && !defined(CV_LINES) ){
			$res = CV_LINES;
		}
		return $res;
	}
}

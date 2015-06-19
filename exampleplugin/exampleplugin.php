<?php



/*
Plugin Name: AdWords URL Tracking Helper
Plugin URI: http://mvdigital.co/#
Description: This plugin helps you to take advantage of Googles new AdWords tracking URLs by providing an interface for you use campaign wide templates for redirection into the correct landing pages and globally available functions for utilising tracking variables within your code.
Author: MV Digital
Version: 0.1
Author URI: http://mvdigital.co
*/

require_once plugin_dir_path(__FILE__) . 'PluginBase.php';

class ADW_Track extends PluginBase {
    
    protected $options = [
        'example-option',
        'example-option-1'
    ];
    
    protected $defaults = [
        'example-option' => 'my option value',
        'example-option1' => 'another option value'
    ];
    
    public function __construct() 
    {
        parent::__construct();
        
        add_action('admin_menu', [$this, 'setupAdminPages']);
        
        session_start();
        
        add_action('init', [$this, 'interceptURL']);
        
        foreach($this->getPluginOptions() as $option => $value) {
            
            if(!$value) {
                update_option($option, $this->defaults[str_replace($this->option_prefix . '-option-', '', $option)]); 
            }
            
        }
        
        
        
    }
    
    public function interceptURL()
    {
        
        if (str_replace('/', '', explode('?', $_SERVER['REQUEST_URI'])[0]) == ADW_TRACKING_URI) {
            $this->setupTracking();
        }
        
    }
    
    public function setupAdminPages() 
    {
        $this->addMenuPage('Adwords Tracking Settings', 'Adwords Tracking', 'manage_options', 'adwords-tracking');
        $this->addSubMenuPage('adwords-tracking', 'Adwords Tracking', 'Settings', 'manage_options', 'adwords-tracking', [$this, 'renderAdminHTML']);
    }
    
    public function renderAdminHTML() 
    {
        $this->adminTemplate('options', ['options' => $this->getPluginOptions(), 'prefix' => $this->option_prefix]);
    }
    
    
    private function setupTracking()
    {
        if(!filter_input(INPUT_COOKIE, $this->getPluginOptions('cookie-name'))) {
            
            $cookie_value = http_build_query(['v' => 1, 's' => session_id()]);
            setcookie($this->getPluginOptions('cookie-name'), $cookie_value, time() + ($this->getPluginOptions('cookie-lifetime') * 86400), $this->getPluginOptions('cookie-path'));

        } else {

            $cookie = [];

            parse_str($_COOKIE[$this->getPluginOptions('cookie-name')], $cookie);

            if($cookie['s'] !== session_id()) {

                $cookie['s'] = session_id();
                $cookie['v']++;

                $cookie_value = http_build_query($cookie);

                setcookie($this->getPluginOptions('cookie-name'), $cookie_value, time() + ($this->getPluginOptions('cookie-lifetime') * 86400), $this->getPluginOptions('cookie-path'));
            }

        }
        
        if(!isset($_SESSION[$this->getPluginOptions('session-param')])) {
            $_SESSION[$this->getPluginOptions('session-param')] = [$this->getTrackingVars()];
        } else {
            $_SESSION[$this->getPluginOptions('session-param')][] = $this->getTrackingVars();
        }
        
        $session_info = end($_SESSION[$this->getPluginOptions('session-param')]);
        
        
        
        if(!$session_info['lpurl']) {
            wp_die('No redirct URL found in tracking URL, please check your tracking template options...');
        } else {
            wp_redirect($session_info['lpurl'], 301);exit;
        }
        
    }
    
    public static function redirect($url) 
    {
        if(stripos($url, 'http://')  !== 0 || stripos($url, 'https://')  !== 0) {
            $url = 'http://' . $url;
        }
        header('HTTP/1.1 301 Moved Permanently'); 
        header('Location: ' . $url); 
    }
    
    public function getTrackingVar($var = false)
    {
        
        $info = filter_input(INPUT_SESSION, $this->getPluginOptions('session-param'));
    
        if(is_array($info[0])) {
            $info = end($info);
        }

        if($var) {
            return $info[$var];
        }

        return $info;
        
    }
    
    private function getTrackingVars() {
    
    $output = []; 
    
    $output['keyword'] = filter_input(INPUT_GET, $this->getPluginOptions('url-template-keyword'));
    $output['matchtype'] = filter_input(INPUT_GET, $this->getPluginOptions('url-template-matchtype'));
    $output['creative'] = filter_input(INPUT_GET, $this->getPluginOptions('url-template-creative'));
    $output['lpurl'] = filter_input(INPUT_GET, $this->getPluginOptions('url-template-lpurl'));
    
    return $output;
    
}
    
    
    
}

ADW_Track::init();





function get_adw_tracking_info($var = false) {
    
    $info = filter_input(INPUT_SESSION, ADW_SESSION_PARAM);
    
    if(is_array($info[0])) {
        $info = end($info);
    }
    
    if($var) {
        return $info[$var];
    }
    
    return $info;
    
}

<?php

define('PLUGIN_BASE_DIR', plugin_dir_path(__FILE__));
define('PLUGIN_BASE_URI', plugin_dir_url(__FILE__));

define('PLUGIN_TEMPLATE_DIR', PLUGIN_BASE_DIR . 'templates');
define('PLUGIN_ADMIN_TEMPLATE_DIR', PLUGIN_BASE_DIR . 'templates/admin');

define('PLUGIN_SCRIPT_DIR', PLUGIN_BASE_DIR . '/js');
define('PLUGIN_STYLE_DIR', PLUGIN_BASE_DIR . '/css');

define('PLUGIN_SCRIPT_URI', PLUGIN_BASE_URI . '/js');
define('PLUGIN_STYLE_URI', PLUGIN_BASE_URI . '/css');


/**
 * Abstract utility class to base a plugin on. 
 * 
 * @author MV DIGITAL
 */
abstract class PluginBase {
    
    protected $option_prefix = false, $options, $settings;
    
    public static $instance;
    
    public function __construct() 
    {
        
        if ((defined('DOING_AJAX') && DOING_AJAX) || is_json_request()) { 
            
            $this->registerAjaxFunctions();
            
            if(empty($_POST)) {
                $_REQUEST = (array)json_decode(file_get_contents('php://input'));
            }
            
        }
        
        if(!$this->option_prefix) {
            $this->option_prefix = strtolower(str_replace('_', '-', get_called_class()));
        }
        
        add_action('admin_init', [$this, 'registerPluginOptions']);
        
        
    }
    
    /**
     * Instantiate Plugin
     * @return type
     */
    public static function init()    
    {   
        
        $class = get_called_class();
        null === $class::$instance AND $class::$instance = new $class;
	return $class::$instance;
        
    }
    
    /**
     * 
     * @param type $handle
     * @param type $file_name
     * @param type $deps
     * @param type $ver
     * @param type $in_footer
     */
    protected function enqueueScript($handle, $file_name = false, $deps = [], $ver = null, $in_footer = true) 
    {   
        if(!$file_name) {
            $src = PLUGIN_SCRIPT_URI . '/' . $handle . '.js';
        } else {
            $src = PLUGIN_SCRIPT_URI . '/' . $file_name;
        }
        
        wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
        
    }
    
    /**
     * 
     * @param type $handle
     * @param type $file_name
     * @param type $deps
     * @param type $ver
     * @param type $media
     */
    protected function enqueueStyle($handle, $file_name = false, $deps = [], $ver = null, $media = 'all')
    {
        
        if(!$file_name) {
            $src = PLUGIN_STYLE_URI . '/' . $handle . '.css';
        } else {
            $src = PLUGIN_STYLE_URI . '/' . $file_name;
        }
        wp_enqueue_style($handle, $src, $deps, $ver, $media);
        
    }
    
    /**
     * 
     * @param type $code
     * @param type $styles
     * @param type $subsets
     */
    protected function addGoogleFont($code, $styles = [300, 400, 600], $subsets = ['latin']) 
    {
        
        $src = '//fonts.googleapis.com/css?family=' . $code . ':';
        $handle = strtolower(str_replace('+', '-', $code));

        if(is_array($styles)) {
            $src .= implode(',', $styles);
        } else {
            $src .= $styles;
        }

        if(!empty($subsets) && is_array($subsets)) {
            $src .= '&subset=' . implode(',', $subsets);
        }

        wp_register_style($handle, $src);
        wp_enqueue_style($handle);
        
    }
    
    /**
     * 
     * @param type $name
     * @param type $data
     * @param type $echo
     * @return type
     */
    protected function template($name, $data = null, $echo = true) 
    {
        
        ob_start();
        
        if($data) extract($data);
        include(PLUGIN_TEMPLATE_DIR . '/' . $name . '.php');
        
        $template = ob_get_clean();
        
        if($echo) {
            echo $template;
        } else {
            return $template;
        }
        
    }
    
    /**
     * 
     * @param type $name
     * @param type $data
     * @param type $echo
     * @return type
     */
    protected function adminTemplate($name, $data = null, $echo = true)
    {
        
        ob_start();
        if($data) extract($data);
        include(PLUGIN_ADMIN_TEMPLATE_DIR . '/' . $name . '.php');
        
        $template = ob_get_clean();
        
        if($echo) {
            echo $template;
        } else {
            return $template;
        }
        
    }
    
    
    /**
     * 
     * @param string $option_name
     */
    public function registerPluginOptions()
    {   
        foreach($this->options as $option_name) {
            register_setting($this->option_prefix . '-group', $this->option_prefix . '-option-' . $option_name);
        }
        
    }
    
    /**
     * 
     * @param type $option_name
     * @return mixed
     */
    protected function getPluginOptions($option_name = null) 
    {
        if($option_name) {
            return get_option($this->option_prefix . '-option-' . $option_name);
        }
        
        $options = [];
        
        foreach($this->options as $option) {
            $options[$this->option_prefix . '-option-' . $option] = get_option($this->option_prefix . '-option-' . $option);
        }
        
        return $options;
        
    }
    
    /**
     * 
     */
    private function registerAjaxFunctions() 
    {
        
        foreach(get_class_methods(get_called_class()) as $method) {
            if(strpos($method, 'ajx_') === 0) {
                
                add_action('wp_ajax_' . str_replace('ajx_', '', $method), array($this, $method));
                add_action("wp_ajax_nopriv_" . str_replace('ajx_', '', $method), array($this, $method));
            }
        }
        
    }
    
    /**
     * 
     * @param string $page_title
     * @param string $menu_title
     * @param string $capability
     * @param string $menu_slug
     * @param string $function
     * @param string $icon_url
     * @param string $position
     */
    protected function addMenuPage($page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = null) 
    {
        add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position);
    }
    
    /**
     * 
     * @param string $parent_slug
     * @param string $page_title
     * @param string $menu_title
     * @param string $capability
     * @param string $menu_slug
     * @param string $function
     */
    protected function addSubMenuPage($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function = '') 
    {
        add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
    }
    
        
    
}


/**
 *  Helpers 
 */

function is_json_request()
{
    
    if (isset($_SERVER["CONTENT_TYPE"])) {
        return strpos($_SERVER["CONTENT_TYPE"], 'application/json') !== false;
    }
    
    return false;
    
}

function render_option_field($prefix, $options, $option) {
    
    echo '<input class="regular-text" type="text" id="' .  $option . '" name="' . $prefix . '-option-' . $option . '" value="' . $options[$prefix . '-option-' . $option] . '" />';
    
}
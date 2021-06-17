<?php
namespace Softx;

class Assets{ 


    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'register_assets']);
        add_action('admin_enqueue_scripts', [$this, 'register_assets']);
       
    }


    public function get_scripts()
    {
        return [
            'dokan-admin-script' => [
                'src' => DEXT_ASSETS. '/js/softx-dokan-admin.js', 
                'version' =>  filemtime(DEXT_DIR. '/assets/js/softx-dokan-admin.js'),
                'deps' => ['jQuery']
            ]
            ];
        
    }


    public function get_styles()
    {
        return [
            'dokan-admin-css' => [
                'src' => DEXT_ASSETS. '/css/softx-dokan-admin.css', 
                'version' => filemtime(DEXT_DIR. '/assets/css/softx-dokan-admin.css'),
                'deps' => ['jQuery']
            ]
            ];
    }


    public function register_assets()
    {
        
        $scripts = $this->get_scripts();

        foreach( $scripts as $handle => $value){ 
            $deps = isset($value['deps']) ? $value['deps'] : false;    
            wp_register_script($handle,  $value['src'],$deps, $value['version'] ,true);
        }


        $styles = $this->get_styles();
        foreach($styles as $handle => $value){
            $deps = isset($value['deps']) ? $value['deps'] : false;    
            wp_register_style($handle , $value['src'], $deps , $value['version']);
        }

    }

    
}
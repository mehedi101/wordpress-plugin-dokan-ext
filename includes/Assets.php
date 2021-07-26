<?php
namespace Softx;

class Assets{ 


    public function __construct() {
        add_action('init', [$this, 'registerAllScripts']);
       
    

        if( is_admin()){ 
            add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        }else{
            add_action('wp_enqueue_scripts', [$this, 'enqueue_front_scripts']);
        }
       
    }

    public function registerAllScripts()
    {
        $styles = $this->get_styles();
        $scripts = $this->get_scripts();
        $this->register_styles( $styles );
        $this->register_scripts( $scripts );

    }


    public function get_scripts()
    {
        return [
            'dokan-admin-script' => [
                'src' => DEXT_ASSETS. '/js/softx-dokan-admin.js', 
                'version' =>  filemtime(DEXT_DIR. '/assets/js/softx-dokan-admin.js'),
                'deps' => ['jquery']
            ],
            'softx-dokan-frontend-js' => [
                'src' => DEXT_ASSETS. '/js/softx-dokan-frontend.js', 
                'version' =>  filemtime(DEXT_DIR. '/assets/js/softx-dokan-frontend.js'),
                'deps' => ['jquery'] 
            ]
            ];
        
    }


    public function get_styles()
    {
        return [
            'dokan-admin-css' => [
                'src' => DEXT_ASSETS. '/css/softx-dokan-admin.css', 
                'version' => filemtime(DEXT_DIR. '/assets/css/softx-dokan-admin.css')
            ],
            'softx-dokan-fronted' => [
                'src' => DEXT_ASSETS. '/css/softx-dokan-frontend.css', 
                'version' => filemtime(DEXT_DIR. '/assets/css/softx-dokan-frontend.css')
            ]
            ];
    }


    public function register_scripts($scripts)
    {

        foreach( $scripts as $handle => $value){ 
            $deps = isset($value['deps']) ? $value['deps'] : false;    
            wp_register_script($handle,  $value['src'],$deps, $value['version'] ,true);
        }



    }

    public function register_styles($styles)
    {
        
        
        foreach($styles as $handle => $value){
            $deps = isset($value['deps']) ? $value['deps'] : false;    
            wp_register_style($handle , $value['src'], $deps , $value['version']);
        }
    }

    public function enqueue_admin_scripts($hook)
    {
        wp_enqueue_script('dokan-admin-script'); 
        wp_enqueue_style('dokan-admin-css'); 
    }

    public function enqueue_front_scripts()
    {
       
        // load softx dokan extension on every page 
            wp_enqueue_style('softx-dokan-fronted');
            wp_enqueue_script( 'softx-dokan-frontend-js');  
        
    }

    
}
<?php
/**
 * Plugin Name: Softx dokan extension
 * Plugin URI: https://softxltd.com 
 * Description: Dokan lite extension for creating and overriding functionality.
 * Version: 1.0.0
 * Author: Mehedi Hasan
 * Author URI: https://mehedihasn.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.7
 * Requires PHP: 7.3
 * Text Domain: softx-dokan
 */

defined('ABSPATH') || exit ;

require_once __DIR__ . '/vendor/autoload.php'; 


final class Dokan_Lite_Extension{ 

    
    public $version='1.0.0';
    
    private static $instance = null;

    private $min_php = '7.3';

    private $container=[];


    private function __construct()
    {

        $this->define_constant();
        register_activation_hook( __FILE__, [$this, 'activate'] );
        register_deactivation_hook( __FILE__, [$this, 'deactivate'] );
        add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
        add_action( 'woocommerce_loaded', [ $this, 'extend_woo' ] );
        add_action( 'dokan_loaded', [ $this, 'extend_dokan' ] );

        
    }


   public static function init()
   {
       if( null === self::$instance ){ 
           self::$instance = new self();
       }
       return self::$instance;

   }


    public function define_constant()
    {
        $this->define('DEXT_VERSION', $this->version);
        $this->define('DEXT_FILE', __FILE__);
        $this->define('DEXT_DIR', __DIR__);
        $this->define('DEXT_INC_DIR', DEXT_DIR.'/includes');
        $this->define('DEXT_URL', plugins_url('', DEXT_FILE));
        $this->define('DEXT_ASSETS', DEXT_URL . '/assets');

    }

    private function define( $name, $value)
    {
        if( ! defined($name)){ 
            define($name, $value); 
        }
    }

    public function activate()
    {
        update_option( 'dext_version', DEXT_VERSION ); 
        (new Softx\Installer())->run();
       
    }

    public function extend_woo(){
        (new Softx\Custom_Taxonomy())->init_taxonomy(); 
    }

    public function extend_dokan()
    {
       ( new Softx\Dokan_Plugin_Override() )->override_dokan(); 
        // add dokan related custom funcationality
    }

    public function deactivate()
    {
        
    }

    public function init_plugin()
    {
     new Softx\Assets(); 

        if(is_admin()){
          new Softx\Admin(); 
        }

    
    }


}

function dokan_ext(){ 

    return Dokan_Lite_Extension::init();
}

dokan_ext();

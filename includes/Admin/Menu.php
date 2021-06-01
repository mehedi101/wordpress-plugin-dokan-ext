<?php 
namespace Softx\Admin;

class Menu{
    
   function __construct()
    {
        add_action( 'admin_menu', [$this, 'wpdocs_register_my_custom_menu_page'] );
    }


    function wpdocs_register_my_custom_menu_page(){
        add_menu_page( 
            __( 'Custom Menu Title', 'softx-dokan' ),
            'Softx Dokan',
            'manage_options',
            'softx-dokan',
            'my_custom_menu_page',
            "dashicons-superhero",
            6
        ); 
    }
   
     
    /**
     * Display a custom menu page
     */
    function my_custom_menu_page(){
        esc_html_e( 'Admin Page Test', 'softx-dokan' );  
    }


}

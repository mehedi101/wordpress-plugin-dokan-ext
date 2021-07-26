<?php
if( !defined('WP_UNINSTALL_PLUGIN')){ 
    wp_die( 
        sprintf('%s should only be called when uninstalling the plugin', __FILE__));
}
remove_role( '150DKK' );
remove_role( '300DKK' );
remove_role( '500DKK' );
remove_role( '800DKK' );
remove_role( '1200DKK' );
remove_role( 'company' );
remove_role( 'employee' );
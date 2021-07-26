<?php
namespace Softx;

class Custom_Taxonomy
{
    public function init_taxonomy(){ 
        
        add_action( 'init', [$this, 'create_prices_hierarchical_taxonomy']);
        add_action( 'init', [$this, 'create_brands_hierarchical_taxonomy']); 
        add_action( 'init', [$this, 'create_shops_taxonomy']); 

    }
    
    /**
     * create wooCommerce product prices taxonomy
     *
     * @return void
     */
    public function create_prices_hierarchical_taxonomy() {
 
        // Add new taxonomy, make it hierarchical like categories
        //first do the translations part for GUI
         
          $labels = array(
            'name' => __( 'Prices', 'softx-dokan' ),
            'singular_name' => __( 'Price', 'softx-dokan' ),
            'search_items' =>  __( 'Search Prices', 'softx-dokan' ),
            'all_items' => __( 'All Prices', 'softx-dokan' ),
            'parent_item' => __( 'Parent Price', 'softx-dokan' ),
            'parent_item_colon' => __( 'Parent Price:', 'softx-dokan' ),
            'edit_item' => __( 'Edit Price', 'softx-dokan' ),
            'update_item' => __( 'Update Price', 'softx-dokan' ),
            'add_new_item' => __( 'Add New Price', 'softx-dokan' ),
            'new_item_name' => __( 'New Price Name', 'softx-dokan' ),
            'menu_name' => __( 'Prices', 'softx-dokan' ),
          );    
         
        // Now register the taxonomy
          register_taxonomy('prices',array('product'), array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'price' ),
          ));
         
    }
        
    /**
     * create wooCommerce product brands taxonomy
     *
     * @return void
     */
    public function create_brands_hierarchical_taxonomy(){
            $labels = [
                'name' => __( 'Brands', 'softx-dokan' ),
                'singular_name' => __( 'Brand', 'softx-dokan' ),
                'search_items' =>  __( 'Search Brands', 'softx-dokan' ),
                'all_items' => __( 'All Brands', 'softx-dokan' ),
                'parent_item' => __( 'Parent Brand', 'softx-dokan' ),
                'parent_item_colon' => __( 'Parent Brand:', 'softx-dokan' ),
                'edit_item' => __( 'Edit Brand', 'softx-dokan' ),
                'update_item' => __( 'Update Brand', 'softx-dokan' ),
                'add_new_item' => __( 'Add New Brand', 'softx-dokan' ),
                'new_item_name' => __( 'New Brand Name', 'softx-dokan' ),
                'menu_name' => __( 'Brands', 'softx-dokan' )
        
            ];
        
            // Now register the taxonomy
            register_taxonomy('brands',array('product'), array(
                'hierarchical' => true,
                'labels' => $labels,
                'show_ui' => true,
                'show_in_rest' => true,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => array( 'slug' => 'brand' ),
            ));
        
    }



    public function create_shops_taxonomy(){

      $labels = [
        'name' => _x('Shops','softx-dokan'),
        'singular_name' => _x('shops','softx-dokan'),
        'menu_name' => __('Shops','softx-dokan'),
        'name_admin_bar' => __('shops','softx-dokan'),
        'search_items' => __('Search Shops','softx-dokan'), 
        'popular_items' => __('Popular Shops','softx-dokan'),
        'all_items' => __('All Shops','softx-dokan'),
        'edit_item' => __('Edit shops','softx-dokan'), 
        'view_item' => __(' View shops','softx-dokan'),
        'update_item' => __('Update shops','softx-dokan'),
        'add_new_item' => __('Add New shops','softx-dokan'),
        'new_item_name' => __('New shops Name','softx-dokan'),
        'not_found' => __('No shops Found','softx-dokan'),
        'no_terms' => __('No Shop', 'softx-dokan'),
        'items_list_navigation' => __(' Shops list navigation','softx-dokan'),
        'items_list' => __('Shops List','softx-dokan'),
        'select_name' => __('Select shops','softx-dokan'),
        'parent_item' => __('Parent shops','softx-dokan'),
        'parent_item_clone' => __('Parent shops:','softx-dokan')
      ];
      $rewrites = [
        'slug' => 'shops',
        'with_front' => true,
        'hierarchical' => false,
        'ep_mask' => EP_NONE


      ];
      $default_term = [ 
          'name' => __( 'private shop', 'softx-dokan' ),
          'slug' => 'private-shop',
          'description' => __('Show this product only for employee')
      ];
      $args = [
                  'public' => true,
                  'show_in_rest' => true,
                  'show_ui' => true,
                  'show_in_nav_menus' => true,
                  'show_tagcloud' => true,
                  'show_admin_column' => true,
                  'hierarchical' => true,
                  'query_var' => 'shops',
                  'rewrites' => $rewrites,
                  'labels' => $labels,
                  'default_term' => $default_term
              ];

      register_taxonomy( 'shops', array('product'), $args );

      $term = term_exists( 'public-shop', 'shops' );

      if( ! $term){ 
        wp_insert_term(
          __( 'public-shop', 'softx-dokan' ),
          'shops', 
          array(
            'slug' => 'public-shop',
            'description' => __( 'shop product on only for general people', 'softx-dokan' ),
          )
        );
      }


    }

}
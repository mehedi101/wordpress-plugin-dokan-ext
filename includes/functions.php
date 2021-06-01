<?php
//hook into the init action and call create_book_taxonomies when it fires
 
add_action( 'init', 'create_prices_hierarchical_taxonomy', 0 );
 
//create a custom taxonomy name it prices for your posts
 
function create_prices_hierarchical_taxonomy() {
 
// Add new taxonomy, make it hierarchical like categories
//first do the translations part for GUI
 
  $labels = array(
    'name' => _x( 'Prices', 'taxonomy general name' ),
    'singular_name' => _x( 'Price', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Prices' ),
    'all_items' => __( 'All Prices' ),
    'parent_item' => __( 'Parent Price' ),
    'parent_item_colon' => __( 'Parent Price:' ),
    'edit_item' => __( 'Edit Price' ), 
    'update_item' => __( 'Update Price' ),
    'add_new_item' => __( 'Add New Price' ),
    'new_item_name' => __( 'New Price Name' ),
    'menu_name' => __( 'Prices' ),
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


  // Display custom Fields on product edit page
add_action('woocommerce_product_options_general_product_data', 'woocommerce_product_custom_fields');

// Save Fields
add_action('woocommerce_process_product_meta', 'woocommerce_product_custom_fields_save');


function woocommerce_product_custom_fields()
{
    global $woocommerce, $post;
    echo '<div class="product_custom_field">';

    woocommerce_wp_checkbox([
      'id' => '_is_public_product_checkbox',
      'label' => __('Is public product', 'softx-dokan'),
      'wrapper_class' => 'show_if_simple',
    ]);

      //Custom Product Number Field
      woocommerce_wp_text_input(
        array(
            'id' => '_public_product_price_field',
            'placeholder' => __('price for the general customer','softx-dokan'),
            'label' => __('Public Price', 'softx-dokan'),
            'type' => 'number',
            'wrapper_class' => 'show_if_simple'
        )
    );

      /*
    // Custom Product Text Field
    woocommerce_wp_text_input(
        array(
            'id' => '_custom_product_text_field',
            'placeholder' => 'Custom Product Text Field',
            'label' => __('Custom Product Text Field', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );
    //Custom Product Number Field
    woocommerce_wp_text_input(
        array(
            'id' => '_custom_product_number_field',
            'placeholder' => 'Custom Product Number Field',
            'label' => __('Custom Product Number Field', 'woocommerce'),
            'type' => 'number',
            'custom_attributes' => array(
                'step' => 'any',
                'min' => '0'
            )
        )
    );
    //Custom Product  Textarea
    woocommerce_wp_textarea_input(
        array(
            'id' => '_custom_product_textarea',
            'placeholder' => 'Custom Product Textarea',
            'label' => __('Custom Product Textarea', 'woocommerce')
        )
    );
    */
    echo '</div>';

}


function woocommerce_product_custom_fields_save($post_id)
{
  /*
    // Custom Product Text Field
    $woocommerce_custom_product_text_field = $_POST['_custom_product_text_field'];
    if (!empty($woocommerce_custom_product_text_field))
        update_post_meta($post_id, '_custom_product_text_field', esc_attr($woocommerce_custom_product_text_field));
// Custom Product Number Field
    $woocommerce_custom_product_number_field = $_POST['_custom_product_number_field'];
    if (!empty($woocommerce_custom_product_number_field))
        update_post_meta($post_id, '_custom_product_number_field', esc_attr($woocommerce_custom_product_number_field));
// Custom Product Textarea Field
    $woocommerce_custom_procut_textarea = $_POST['_custom_product_textarea'];
    if (!empty($woocommerce_custom_procut_textarea))
        update_post_meta($post_id, '_custom_product_textarea', esc_html($woocommerce_custom_procut_textarea));
 */
 $wc_custom_product_cehckbox_fields =$_POST['_is_public_product_checkbox'];
 
if(!empty($wc_custom_product_cehckbox_fields)){ 
  update_post_meta($post_id, '_is_public_product_checkbox', esc_attr($wc_custom_product_cehckbox_fields));
}

$wc_public_product_price_field = $_POST['_public_product_price_field'];

if(!empty($wc_custom_product_cehckbox_fields)){ 
  update_post_meta($post_id, '_public_product_price_field', esc_attr($wc_public_product_price_field));
}

}


// custom css and js
add_action('admin_enqueue_scripts', 'cstm_css_and_js');
 
function cstm_css_and_js($hook) {
    // your-slug => The slug name to refer to this menu used in "add_submenu_page"
        // tools_page => refers to Tools top menu, so it's a Tools' sub-menu page
    // if ( 'tools_page_your-slug' != $hook ) {
    //     return;
    // }
 
    wp_enqueue_script('boot_js', plugins_url('assets/js/softx-dokan-admin.js',__FILE__ ));
}

<?php
//hook into the init action and call create_book_taxonomies when it fires
 
add_action( 'init', 'create_prices_hierarchical_taxonomy', 0 );
add_action( 'init', 'create_brands_hierarchical_taxonomy', 0 );

//create a custom taxonomy name it prices for your posts
 
function create_prices_hierarchical_taxonomy() {
 
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


function create_brands_hierarchical_taxonomy(){
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

/*
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
    echo '</div>';

}


function woocommerce_product_custom_fields_save($post_id)
{
 $wc_custom_product_cehckbox_fields =$_POST['_is_public_product_checkbox'];
 
if(!empty($wc_custom_product_cehckbox_fields)){ 
  update_post_meta($post_id, '_is_public_product_checkbox', esc_attr($wc_custom_product_cehckbox_fields));
}

$wc_public_product_price_field = $_POST['_public_product_price_field'];

if(!empty($wc_custom_product_cehckbox_fields)){ 
  update_post_meta($post_id, '_public_product_price_field', esc_attr($wc_public_product_price_field));
}

}
*/

// custom css and js
//add_action('admin_head', 'cstm_css_and_js');
add_action('admin_enqueue_scripts', 'cstm_css_and_js');
 
function cstm_css_and_js() {
 
    wp_enqueue_style('boot_admin_css', plugins_url('../assets/css/softx-dokan-admin.css',__FILE__ ));
}

#Remove product categories from shop page

//add_action( 'pre_get_posts', 'softx_custom_pre_get_posts_query' );
//add_action( 'woocommerce_product_query', 'softx_custom_pre_get_posts_query' );

function softx_custom_pre_get_posts_query( $q ) {
 
	if ( ! $q->is_main_query() ) return;
	if ( ! $q->is_post_type_archive() ) return;
	
	if ( ! is_admin() && is_shop() && ( ! is_user_logged_in() || current_user_can( 'customer'))) {
  
   # get all the terms id of prices taxonomy; 
  //$price_terms = get_terms('prices', ['hide_empty' => 1, 'fields' => 'ids']);
 
	/* $q->set( 'tax_query', array(
      array(
			'taxonomy' => 'prices',
			'field' => 'id',
			'terms' => $price_terms, // Don't display products in these categories on the shop page
			'operator' => 'NOT IN'
		  )
  ));  */
  $meta_query = array(
    'relation' => 'AND',
    array(
       'key'=>'_is_public_product_checkbox',
       'value'=>'yes'
    ),
);

$q->set( 'meta_query', $meta_query );
	
	}elseif(! is_admin() && is_shop() &&  is_user_logged_in()){
    $price_terms = get_terms('prices', ['hide_empty' => 1, 'fields' => 'ids']);
     $q->set( 'tax_query', array(
      array(
			'taxonomy' => 'prices',
			'field' => 'id',
			'terms' => $price_terms, // Don't display products in these categories on the shop page
			'operator' => 'IN'
		  )
  ));  

   }
 
 
}


add_filter('woocommerce_product_get_price', 'softx_custom_price_for_public_visitor', 10, 2);
/**
 * custom_price_WPA111772 
 *
 * filter the price based on category and user role
 * @param  $price   
 * @param  $product 
 * @return 
 */
function softx_custom_price_for_public_visitor($price, $product) {
  $is_public = $product->get_meta('_is_public_product_checkbox');
  

    if (is_user_logged_in() 
    && ( current_user_can('administrator') || current_user_can('manage_company') ) ){ 
      return $price;
    }elseif(is_user_logged_in() && current_user_can('employee')){
      return $price=""; 
    }else{

      if( $is_public == 'yes'){ 
        $price = $product->get_meta('_public_product_price_field', true ); 
      } 
      return $price;
    }
    
    
}
add_action( 'flatsome_after_header', 'softx_public_shop_term_header');
	function softx_public_shop_term_header()
	{
	//	if( has_term('public-shop','shops')){  
		if( is_tax('shops','public-shop')){  
     
        echo	do_shortcode( '[block id="public-shop-header"]' );

		}

		
	}

/**
 * Hide product for current user role
 */

// add_action( 'woocommerce_product_query', 'hide_product_query' );

// function hide_product_query( $q ){

//   if((getCurrentUserRole() == 'editor' ) || (getCurrentUserRole() == 'administrator' )){

// return false;
// } else  {


// $meta_query = $q->get( 'meta_query' );

//     if ( get_option( 'woocommerce_hide_out_of_stock_items' ) == 'no' ) {
//         $meta_query[] = array(
//                     'key'       => '_hide_from_users',
//                     'compare'   => 'NOT EXISTS'
//                 );
//     }

//     $q->set( 'meta_query', $meta_query );

// }


// }

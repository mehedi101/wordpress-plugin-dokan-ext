<?php
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
    && ( current_user_can('administrator') || current_user_can('manage_company') ||  current_user_can('employee') ) ){ 
      return $price;
    }else{

      if( $is_public == 'yes'){ 
        $price = $product->get_meta('_public_product_price_field', true ); 
      } 
      return $price;
    }
    
}

/**
 * 
 * @author Mehedi Hasan <hello@mehedihasn.com>
 * 
 * add banner to the public-shop Page
 * @return void
 * 
 */
add_action( 'flatsome_after_header', 'softx_public_shop_term_header');

	function softx_public_shop_term_header()
	{
  

	//	if( has_term('public-shop','shops')){  
		if( is_tax('shops','public-shop')){  
        $term = get_term_by('slug','public-shop', 'shops');
          $shortcode =  get_term_meta( $term->term_id, 'top_content', true );
        echo !empty($shortcode) ?	do_shortcode( $shortcode ) : null;

		}

		
	}

/**
 * 
 * @author Mehedi Hasan <hello@mehedihasn.com>
 * 
 * Shop public price column on admin product list table
 * 
 * @param array $columns
 * @return mixed 
 */

add_filter( 'manage_edit-product_columns', 'softx_add_public_price_column', 11);

function softx_add_public_price_column($columns)
{
  $columns['_public_product_price_field'] = __( 'public price', 'softx-dokan');

  return $columns;
}


/**
 * 
 * @author Mehedi Hasan <hello@mehedihasn.com>
 * 
 * show content to the public price column on
 * woocommerce admin product list table. 
 * 
 * @param array $column
 * @param int $product_id
 * @return void
 * 
 */
add_filter( 'manage_product_posts_custom_column', 'softx_show_public_price_content',10,2);

function softx_show_public_price_content($column, $product_id){  

  switch($column) {

    case '_public_product_price_field':
      $public_price = get_post_meta( $product_id, '_public_product_price_field', true );
      echo ($public_price > 1) ? wc_price($public_price) : null;
      break;
    
    default:
      break;
  }
}

/**
 * Disable add to cart for general visitors or customer
 * for only private shop products
 * @return void
 */
add_action( 'wp_head','softx_disable_product_purchase',10,2);

function softx_disable_product_purchase(){

if (is_product() && !is_user_logged_in() && ! has_term( 'public-shop', 'shops')) {
  // in product page
  add_filter('woocommerce_is_purchasable', '__return_false');
  }
}


/**
 * add role as a class name for in the html body
 * @return  array
 */
add_filter('body_class','softx_add_custome_css_class_to_body');
function softx_add_custome_css_class_to_body($classes) {

  if(is_user_logged_in()){ 
    $user = wp_get_current_user();
    $roles = ( array ) $user->roles;

    // add role as 'class-name' to the $classes array
    $classes[] = $roles[0];
  }
  
  // return the $classes array
  return $classes;
}






<?php

function softx_get_maximum_order_amount_by_employee(){ 
  /// we will work on it 
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

  if (is_user_logged_in()  ){ 
      return $price;
    }else{
      $price = "";
      
      } 
      return $price;
    }
   
    

/**
 * Redirect employee to shop page afert login
 * @since  1.0.0
 * @param string $redirect_to
 * @param  object $user
 * @author Mehedi Hasan <hello@mehedihasn.com>
 * 
 * @return string [url]
 */

function softx_redirecet_employee_after_login($redirect_to, $user)
{
  if(user_can($user, 'manage_employee')){ 
    $redirect_to = wc_get_page_permalink( 'myaccount' );
  }elseif ( user_can( $user, 'employee' ) ) {
      $redirect_to = wc_get_page_permalink( 'shop' );

  }
  return $redirect_to;
}
add_filter('woocommerce_login_redirect', 'softx_redirecet_employee_after_login', 1,2);

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
    if(!empty($roles) && !is_wp_error($roles[0])){
    $classes[] = $roles[0];
	}	
  }

  
  // return the $classes array
  return $classes;
}

/*******************************
 * My account page customization 
 *******************************/

/*=== Account page new tab ===*/
function softx_my_account_add_remove_menu_items( $items ) {
  // Remove the logout menu item.
  $logout = $items['customer-logout'];
  unset( $items['customer-logout'] );
    if(current_user_can( 'customer' )){
      // Insert your custom endpoint.
      $items['reorder-form'] = 'Genbestil';
      $items['employee-list'] = 'Medarbejderliste';
      $items['gift-order-list'] = 'Ordreliste';
    } 
    if(current_user_can( 'administrator' )){
      $items['company-list'] = 'Alle Firmaer';
      $items['brand-list'] = 'Alle brand';
    }
  // Insert back the logout item.
  $items['customer-logout'] = $logout;
  return $items;
  }   
  add_filter( 'woocommerce_account_menu_items', 'softx_my_account_add_remove_menu_items' );
  
  
  add_filter ( 'woocommerce_account_menu_items', 'misha_remove_my_account_links' );
  function misha_remove_my_account_links( $menu_links ){ 
    unset( $menu_links['edit-address'] ); // Addresses
    //unset( $menu_links['dashboard'] ); // Remove Dashboard
    //unset( $menu_links['payment-methods'] ); // Remove Payment Methods
    unset( $menu_links['orders'] ); // Remove Orders
    unset( $menu_links['downloads'] ); // Disable Downloads
    //unset( $menu_links['edit-account'] ); // Remove Account details tab
    //unset( $menu_links['customer-logout'] ); // Remove Logout link 
    return $menu_links; 
  }
  


/**
 * Adding new funcaitonality to the WooCommerce cart page
 * for employee and company only
 * @since 1.0.0
 * @author Mehedi Hasan <hello@mehedihasn.com>
 *  
 **/
function softx_custom_message_after_cart_table(){  
  if( ! is_user_logged_in()){
  return;
  }
  if(current_user_can('employee') && ! current_user_can('administrator')){
    $cart_amt =  WC()->cart->subtotal;
    $currentuserRole= wp_get_current_user()->roles[0];
    $maximum =(int) str_replace("dkk","",$currentuserRole);

    if($cart_amt > $maximum){
      remove_action( 'woocommerce_proceed_to_checkout','woocommerce_button_proceed_to_checkout', 20);
      echo "Du skal maximum have ".wc_price($maximum)." i din kurv for at bestille en ordre, din nuværende kurv total er ".wc_price($cart_amt);
    }
  }

}
add_action( 'woocommerce_after_cart_table', 'softx_custom_message_after_cart_table');

























/****************************************************************
 * This is for public shop don't need Now
 * **************************************************************
 */

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




/**
 * 
 * @author Mehedi Hasan <hello@mehedihasn.com>
 * 
 * add banner to the public-shop Page
 * @return void
 * 
 */
//add_action( 'flatsome_after_header', 'softx_public_shop_term_header');

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

//add_filter( 'manage_edit-product_columns', 'softx_add_public_price_column', 11);

function softx_add_public_price_column($columns)
{
  $columns['_public_product_price_field'] = __( 'public price', 'softx-dokan');
 $columns['author'] = __( 'Butikker', 'softx-dokan');	

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
//add_filter( 'manage_product_posts_custom_column', 'softx_show_public_price_content',10,2);

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








  

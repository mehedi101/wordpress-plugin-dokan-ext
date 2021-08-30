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

  if (! is_user_logged_in()  ){ 
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
    if(current_user_can( 'company' )){
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
   * Get company order details by 
   *
   * @param int $loginuser_id
   * @return array || object
   */
  function softx_get_company_order_info_by_employee_id($loginuser_id){ 
    // show product delivery address.

    $get_order_id = get_user_meta($loginuser_id, 'company_order_id', true);
    $get_company_id = get_user_meta($loginuser_id, 'company_id', true);
    global $wpdb;

    $sql= $wpdb->prepare("SELECT
    {$wpdb->prefix}orderinfo.price_category, 
    {$wpdb->prefix}orderinfo.delivery_type, 
    {$wpdb->prefix}orderinfo.expire_date AS delivery_date, 
    {$wpdb->prefix}orderformdata.company,
    {$wpdb->prefix}orderformdata.address AS company_address
    FROM
    {$wpdb->prefix}orderformdata
    INNER JOIN
    {$wpdb->prefix}orderinfo
    ON 
    {$wpdb->prefix}orderformdata.id = {$wpdb->prefix}orderinfo.company_id
    WHERE
    {$wpdb->prefix}orderinfo.id = %d AND
    {$wpdb->prefix}orderinfo.company_id = %d", $get_order_id, $get_company_id);

    $result = $wpdb->get_row($sql);

    return $result;

  }

 /**
 * showing delivery address to the WooCommerce cart page
 * for employee and company only
 * @since 1.0.0
 * @author Mehedi Hasan <hello@mehedihasn.com>
 * @return  string
 **/ 
function softx_show_delivery_address(){ 
$d_address = [];
$loginuser_id = get_current_user_id();
// get company order details by logged in user. 
$result = softx_get_company_order_info_by_employee_id($loginuser_id); 

$d_address['company'] = $result->company;
if( $result->delivery_type == 'company'){ 
  $d_address['company_addr'] = $result->company_address;
}else{

  foreach( WC()->cart->get_cart() as $cart_item ){


    $post_obj    = get_post( $cart_item['product_id'] ); // The WP_Post object
    $list .= sprintf("<tr><td>%s</td><td>%s</td></tr>",
                  $post_obj->post_title , dokan_get_seller_address( $post_obj->post_author  )
              ) ;

              $d_address['vendor_addr'][$post_obj->post_title] = dokan_get_seller_address( $post_obj->post_author  );    
   
  }
}

	
  // return  $delivery_address;
  return  $d_address;
 
  
}



/**
 * Adding new funcaitonality to the WooCommerce cart page
 * for employee and company only
 * @since 1.0.0
 * @author Mehedi Hasan <hello@mehedihasn.com>
 * @return  void
 **/
function softx_custom_message_after_cart_table(){  
  if( ! is_user_logged_in()  && ! is_cart()){
  return;
  }
  if(current_user_can('employee') && ! current_user_can('administrator')){
    $cart_amt =  WC()->cart->subtotal;
    $currentuserRole= wp_get_current_user()->roles[0];
    $maximum =(int) str_replace("dkk","",$currentuserRole);

    // show if cart amount is over the employee per order.
    if($cart_amt > $maximum){
      remove_action( 'woocommerce_proceed_to_checkout','woocommerce_button_proceed_to_checkout', 20);


      wc_print_notice(
				sprintf( 'Du skal maximum have %s i din kurv for at bestille en ordre, din nuværende kurv total er %s.' , 
				wc_price($maximum), 
				wc_price($cart_amt) 
				), 'error'
			);
    }else{
      if($cart_amt > 1 && $cart_amt <= $maximum){ 
        //* show address if available
      //  wc_print_notice(softx_show_delivery_address(), 'success');
       // echo  softx_show_delivery_address();
        $address = softx_show_delivery_address();
    //   var_export( softx_show_delivery_address());

        if(array_key_exists('company_addr',  $address )){ 
          echo  
          "<p class='delivery_address'>
              <strong>afhenter gaven fra</strong>
              <br/>
              <address>{$address['company_addr']}</address>
          </p>";
        }elseif(array_key_exists('vendor_addr',  $address )){ 
          $list ="<table class='delivery_address'>";
          $list .= "<tr><th>vare</th><th>afhenter gaven ved forretningen </th></tr>";

          foreach($address['vendor_addr'] as $title => $addr ){ 
            $list .= sprintf("<tr><td>%s</td><td>%s</td></tr>",
            $title , $addr) ;
          }
          $list .="</table>";
         echo $list;
        }else{ 
          var_export($address);
        }

      }
    }
  }
// 
}
// show delivery address after cart table 
add_action( 'woocommerce_after_cart_table', 'softx_custom_message_after_cart_table');


/**
 * Checkout page customization
 * @author Mehedi Hasan <hello@mehedihasn.com>
 * @since 1.0.0
 */
// show delivery address after checkout billing form 
add_action( 'woocommerce_after_checkout_form', 'softx_custom_message_after_cart_table');
//add_action( 'woocommerce_after_cart', 'softx_custom_message_after_cart_table');

// Removes Order Notes Title - Additional Information & Notes Field

add_filter( 'woocommerce_enable_order_notes_field', '__return_false', 9999 );
//remove payment 
add_filter( 'woocommerce_cart_needs_payment', '__return_false' );



//add_filter( 'woocommerce_product_query', 'softx_custom_pre_get_posts_query',9999 );

function softx_custom_pre_get_posts_query( $meta_query ) {
 
	if (  is_admin() || ! is_user_logged_in()) return;
  $user = wp_get_current_user();
  global $wpdb;
  $sql = "SELECT slug FROM {$wpdb->prefix}terms AS t INNER JOIN {$wpdb->prefix}term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('prices') ORDER BY t.slug ASC";
  $roles = $wpdb->get_col($sql);
 // var_export($prices);
  //wp_die(); 
 // $roles = ['150dkk','200dkk','300dkk','500dkk','800dkk','1200dkk'];
	if (  is_shop() &&  in_array($user->roles[0], $roles)) {
    $rolePrice = (int) str_replace('dkk',"", $user->roles[0]);
    $meta_query[] = [
        'key' => '_price',
        'value' => $rolePrice,
        'compare' => '<=',
        'type' => 'NUMERIC'
      ];
	
	}
  return $meta_query;
 
}

add_filter('woof_get_meta_query', 'softx_custom_pre_get_posts_query');

/*add meta data to the order item*/ 
/**
 * Add meta data to the order item. 
 * this funcationality has not created yet.
 */
add_action( 'woocommerce_checkout_update_order_meta', 'action_function_name_9603', 10, 2 );
function action_function_name_9603( $order_id, $data ){
	// action...
}


/* add_action('woocommerce_checkout_create_order_line_item', 'action_checkout_create_order_line_item', 10, 4 );
function action_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
    $item->update_meta_data( '_company_name', 'DC Company' );
} */

add_action( 'woocommerce_add_order_item_meta', 'add_order_item_meta', 10, 2 );

function add_order_item_meta($item_id, $cart_item) {

  $loginuser_id = get_current_user_id();
  $get_company_order = softx_get_company_order_info_by_employee_id($loginuser_id);
  $product = get_post( $cart_item['product_id']);
  $vendor  = dokan()->vendor->get( $product->post_author );
    
    $custom_meta_data = [];
    $custom_meta_data['_company_name'] = $get_company_order->company;
    $custom_meta_data['_venodor_shop'] = $vendor->get_shop_name();
    $custom_meta_data['_delivery_type'] = $get_company_order->delivery_type;
   // $custom_meta_data['_order_created'] = "2021-8-12";
    $custom_meta_data['_delivery_date'] = $get_company_order->delivery_date;

    foreach($custom_meta_data as $key => $value){ 
      wc_update_order_item_meta($item_id, $key, $value);
    }

   
}















/****************************************************************
 * This is for public shop don't need Now
 * **************************************************************
 */

//add_action( 'pre_get_posts', 'softx_custom_pre_get_posts_query' );
//add_action( 'woocommerce_product_query', 'softx_custom_pre_get_posts_query' );
/* 
function softx_custom_pre_get_posts_query( $q ) {
 
	if ( ! $q->is_main_query() ) return;
	if ( ! $q->is_post_type_archive() ) return;
	
	if ( ! is_admin() && is_shop() && ( ! is_user_logged_in() || current_user_can( 'customer'))) {
  
   # get all the terms id of prices taxonomy; 
  //$price_terms = get_terms('prices', ['hide_empty' => 1, 'fields' => 'ids']);
 
 $q->set( 'tax_query', array(
      array(
			'taxonomy' => 'prices',
			'field' => 'id',
			'terms' => $price_terms, // Don't display products in these categories on the shop page
			'operator' => 'NOT IN'
		  )
  ));  
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
 
 
} */




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








  

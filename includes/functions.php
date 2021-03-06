<?php
/**
 * check an user is an employee or not
 *
 * @param int $id
 * @return mixed
 */
function is_employee($id=null) { 
  if (  ! is_user_logged_in()  || current_user_can('administrator') ) return false;

  $id = !empty($id) ? (int) $id : get_current_user_id();
  $user = get_user_by('id', $id);

  global $wpdb;
  $sql = "SELECT slug FROM {$wpdb->prefix}terms AS t INNER JOIN {$wpdb->prefix}term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('prices') ORDER BY t.slug ASC";
  $roles = $wpdb->get_col($sql);
 // var_export($prices);
  //wp_die(); 
	if ( in_array(strtolower($user->roles[0]), $roles)){ 
    return [
      'id' => $user->ID,
      'role' => $user->roles[0],
      'name' => $user->display_name,
      'emp_email' => $user->user_email,
      'company_id' => get_user_meta($user->ID, 'company_id', true),
      'order_id' => get_user_meta($user->ID, 'company_order_id', true)
    ];
  }
  return false;

}

/**
 * Get total order amount of a exsiting customer 
 *
 * @param int $emp_id
 * @return int
 */
function softx_get_total_order_amount_by_employee($emp_id = null){ 
  $emp_id = ($emp_id)??get_current_user_id();
  global $wpdb;
  $sql =$wpdb->prepare("SELECT
  IFNULL(SUM(wp_wc_order_stats.net_total), 0	) as total
  FROM
    wp_wc_order_stats
    INNER JOIN
    wp_postmeta
    ON 
      wp_wc_order_stats.order_id = wp_postmeta.post_id
  WHERE
    wp_postmeta.meta_key = '_customer_user' AND
    wp_postmeta.meta_value = %d", $emp_id);

    return $wpdb->get_var($sql);

}

add_filter('woocommerce_product_get_price', 'softx_custom_price_for_public_visitor', 10, 2);

/* added css in softx-dokan plugin forntend.css line 104 to hide content. 
//add_filter('woocommerce_product_variation_get_price', 'softx_custom_price_for_public_visitor', 10, 2);

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
  function softx_get_company_order_info_by_employee_id($loginuser_id, $get_order_id, $get_company_id){ 
    // show product delivery address.
    global $wpdb;

    $sql= $wpdb->prepare("SELECT
    {$wpdb->prefix}orderinfo.price_category, 
    {$wpdb->prefix}orderinfo.delivery_type, 
    {$wpdb->prefix}orderinfo.expire_date AS delivery_date, 
    {$wpdb->prefix}orderformdata.company,
    {$wpdb->prefix}orderformdata.contact_person,
    {$wpdb->prefix}orderformdata.email,
    {$wpdb->prefix}orderformdata.direct_phone,
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
 * Adding new funcaitonality to the WooCommerce cart page
 * for employee and company only
 * @since 1.0.0
 * @author Mehedi Hasan <hello@mehedihasn.com>
 * @return  void
 **/
function softx_check_employee_maximum_buying_amount(){  
  if( ! is_user_logged_in()  && ! is_cart()){
  return;
  }
  $is_emp = is_employee();

  $previous_order = softx_get_total_order_amount_by_employee($is_emp['id']);
  if($is_emp){
    $cart_amt =  WC()->cart->subtotal;
   // $currentuserRole= wp_get_current_user()->roles[0];
    $maximum =(int) str_replace("dkk","",strtolower($is_emp['role']));

    // show if cart amount is over the employee per order.
    if($cart_amt > $maximum){
      remove_action( 'woocommerce_proceed_to_checkout','woocommerce_button_proceed_to_checkout', 20);


      wc_print_notice(
				sprintf( 'Du skal maximum have %s i din kurv for at bestille en ordre, din nuv??rende kurv total er %s.' , 
				wc_price($maximum), 
				wc_price($cart_amt) 
				), 'error'
			);
    }elseif($cart_amt < $maximum){
      remove_action( 'woocommerce_proceed_to_checkout','woocommerce_button_proceed_to_checkout', 20);


      wc_print_notice(
        
				sprintf( 'Du skal minimum have %s i din kurv for at bestille en ordre din nuv??rende kurv total er %s.' , 
				wc_price($maximum), 
				wc_price($cart_amt) 
				), 'error'
			);

    }elseif($previous_order > 0){
      remove_action( 'woocommerce_proceed_to_checkout','woocommerce_button_proceed_to_checkout', 20);
      wc_print_notice('Du har allerede lavet en bestilling, hvis du har fortrudt dit valg bedes du kontakte Holstebro Handelsstandsforening.'
				 ,'error'
			);

    }else{ 
      return;
    }
  }
// 
}

add_action( 'woocommerce_after_cart_table', 'softx_check_employee_maximum_buying_amount');
/**
 * Checkout page customization
 * @author Mehedi Hasan <hello@mehedihasn.com>
 * @since 1.0.0
 */
// show delivery address after checkout billing form 
add_action( 'woocommerce_after_checkout_form', 'softx_check_employee_maximum_buying_amount');

function softx_get_vendor_shop_info($product_id){ 
  $product = get_post( $product_id);
  $store_info        = dokan_get_store_info( $product->post_author);
  $vendor_shop_name  = $store_info['store_name'];
  $vendor_delivery_addr = $store_info['address']['street_1'] ;
  $vendor_delivery_addr .= !empty(trim($store_info['phone']))?", Telefon: ". $store_info['phone'] : null;
  
  return [  'name' => $vendor_shop_name, 'adresse' => $vendor_delivery_addr];
}



function prefix_update_existing_cart_item_meta() {
	$cart = WC()->cart->cart_contents;

  $is_emp = is_employee();
  if($is_emp){

  $get_company_order = softx_get_company_order_info_by_employee_id($is_emp['id'], $is_emp['order_id'], $is_emp['company_id']);

  $delivery_type = $get_company_order->delivery_type;

	foreach( $cart as $cart_item_id=>$cart_item ) {
   $cart_item['firma'] =$get_company_order->company;
   $cart_item['afhentningsdato'] =$get_company_order->delivery_date;
   $cart_item['kontakt_person'] =$get_company_order->contact_person;
   $cart_item['firma_email'] =$get_company_order->email;
   $cart_item['firma_telefon'] =$get_company_order->direct_phone;
   $vendor = softx_get_vendor_shop_info($cart_item['product_id']);
   $cart_item['butik'] = $vendor['name'];
   $cart_item['afhentnings_steder'] =$delivery_type;
  if( $delivery_type == 'firma'){ 
    $cart_item['adresse'] = $get_company_order->company_address;
  }else{ 
    $cart_item['adresse'] = $vendor['adresse'];
  }  
	WC()->cart->cart_contents[$cart_item_id] = $cart_item;
	}
	WC()->cart->set_session();

  }
  // is emp end; 
   }  
   add_action( 'woocommerce_before_calculate_totals','prefix_update_existing_cart_item_meta', 10, 1);


/**
 * Display custom item data in the cart
 */
function plugin_republic_get_item_data( $item_data, $cart_item_data ) {

  if( isset( $cart_item_data['afhentnings_steder'])  ){ 
   if($cart_item_data['afhentnings_steder'] == 'firma' ){
    $item_data[] = array(
      'key' => __( 'Afhentnings steder', 'softx-dokan' ),
      'value' =>  "<strong>Firma: {$cart_item_data['firma']}</strong><span>{$cart_item_data['adresse']}</span>"
    );
   }else{
    $item_data[] = array(
      'key' => __( 'Afhentnings steder', 'softx-dokan' ),
      'value' =>  "<strong>Butik: {$cart_item_data['name']}</strong><span>{$cart_item_data['adresse']}</span>"
    );
   }
  }

	return $item_data;
  }
add_filter( 'woocommerce_get_item_data', 'plugin_republic_get_item_data', 10, 2 ); 

/**
 * Add custom meta to order and order item as well.
 */

  function plugin_republic_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
 
    
    if( isset( $values['firma'] ) ) {
      $item->add_meta_data( __( 'firma', 'softx-dokan' ), $values['firma'], true );
      $order->update_meta_data( __( 'firma', 'softx-dokan' ), $values['firma'], true );
    }

    if( isset( $values['kontakt_person'] ) ) {
      $order->update_meta_data( __( 'kontakt_person', 'softx-dokan' ), $values['kontakt_person'], true);
    }

    if( isset( $values['firma_email'] ) ) {
      $order->update_meta_data( __( 'firma_email', 'softx-dokan' ), $values['firma_email'], true );
    }

    if( isset( $values['firma_telefon'] ) ) {
      $order->update_meta_data( __( 'firma_telefon', 'softx-dokan' ), $values['firma_telefon'], true );
    }
   
    if( isset( $values['afhentningsdato'] ) ) {
      $order->update_meta_data( __( 'afhentningsdato', 'softx-dokan' ), $values['afhentningsdato'], true );
    }  

    if( isset( $values['butik'] ) ) {
      $item->add_meta_data( __( 'butik', 'softx-dokan' ), $values['butik'], true );
    }
    if( isset( $values['afhentnings_steder'] ) ) {
      $order->update_meta_data( __( 'afhentnings_steder', 'softx-dokan' ), $values['afhentnings_steder'], true );
    }
    if( isset( $values['adresse'] ) ) {
      $order->update_meta_data( __( 'adresse', 'softx-dokan' ), $values['adresse'], true );
    }
  
   }
   add_action( 'woocommerce_checkout_create_order_line_item', 'plugin_republic_checkout_create_order_line_item', 10, 4 );


/*
   add_action('woocommerce_checkout_create_order', 'before_checkout_create_order', 20, 2);
   function before_checkout_create_order( $order, $data ) {

      var_export( $order);
     // wp_die();
       $order->update_meta_data( '_custom_meta_key', 'mehedi' );
   }
*/


// Removes Order Notes Title - Additional Information & Notes Field

add_filter( 'woocommerce_enable_order_notes_field', '__return_false', 9999 );
//remove payment 
add_filter( 'woocommerce_cart_needs_payment', '__return_false' );



//add_filter( 'woocommerce_product_query', 'softx_custom_pre_get_posts_query',9999 );

function softx_custom_pre_get_posts_query( $meta_query ) {
 
  $is_emp = is_employee();
	if (  is_shop() &&  $is_emp ) {
    $rolePrice = (int) str_replace('dkk',"", $is_emp['role']);
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

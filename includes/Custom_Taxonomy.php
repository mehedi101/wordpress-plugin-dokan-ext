<?php
namespace Softx;

class Custom_Taxonomy
{
    public function init_taxonomy(){ 
        
        add_action( 'init', [$this, 'create_prices_hierarchical_taxonomy']);
        add_action( 'init', [$this, 'create_brands_hierarchical_taxonomy']); 
        add_action( 'init', [$this, 'create_shops_taxonomy']); 
        add_action( 'shops_edit_form_fields', [$this, 'edit_shops_form_fields'], 10,2);
        add_action( 'edited_shops', [$this, 'update_top_content_term_meta'], 10,2);

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

          $prices_terms = ['150DKK','200DKK','300DKK','500DKK','800DKK','1200DKK'];
          foreach($prices_terms as $term){  
            $this->softx_create_taxonomy_term($term, 'prices');
          }
         
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

            $brands_terms = ['BayMartin','Guarantor'];
            foreach($brands_terms as $term){  
              $this->softx_create_taxonomy_term($term, 'brands');
            }
            
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

      $this->softx_create_taxonomy_term('public-shop', 'shops');

    }

    /**
     * Show top content meta key on the shop taxonomy.
     * @since 1.0.0
     */

    public function edit_shops_form_fields($term, $taxonomy){ 

      $get_top_content_meta = trim(get_term_meta( $term->term_id, 'top_content', true ));

    ?> 
    <tr class="form-field term-public-shop-wrap">
			<th scope="row">
        <label for="top_content"> <?php _e( 'Top content', 'softx-dokan' )?> </label>
      </th>
			<td>
      <textarea name="top_content" id="top_content" rows="3" cols="50" class="large-text">
        <?php echo $get_top_content_meta; ?>
      </textarea>  
			<p class="description"> <?php _e( 'Add block banner shortcode to show on the term header.', 'softx-dokan' );?> </p>
    </td>
		</tr>
    <?php   
    }

    public function update_top_content_term_meta($term_id, $tt_id){  

      if( ! current_user_can('manage_options') && empty($_POST['top_content'])){  
        return;
      }
      $shortcode = trim($_POST['top_content']);
      update_term_meta($term_id, 'top_content',  $shortcode);
      
    }

    private function softx_create_taxonomy_term($term_name, $taxonomy){  

      if(! current_user_can('manage_options')){ 
        return false;
      }
      $slug = sanitize_title($term_name);

      $term = term_exists( $slug, $taxonomy );

      if( ! $term){ 
       $term= wp_insert_term(
          __( $term_name, 'softx-dokan' ),
          $taxonomy, 
          array(
            'slug' => $slug,
            'description' => __( $term_name, 'softx-dokan' ),
          )
        );
      }

      return $term;
    }


}
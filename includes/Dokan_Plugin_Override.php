<?php
namespace Softx;
class Dokan_Plugin_Override
{
    public function override_dokan( )
    {


        /**
         * Show fields on add new product vendor dashboard. 
         */
        add_action('softx_dokan_new_product_after_title_tag', [$this, 'softx_add_fields_after_title'], 10); 

      //  add_action( 'dokan_new_product_after_product_tags',[$this, 'brands_tax_field'],10 );
        /*
        * Saving product field data for edit and update
        */

        add_action( 'dokan_new_product_added',[$this, 'save_add_product_meta'], 10, 2 );
        add_action( 'dokan_product_updated', [$this, 'save_add_product_meta'], 10, 2 );

        /*
        * Showing fields data on product edit page
        */
        
       //  add_action('dokan_product_edit_after_product_tags',[$this, 'show_on_edit_page'],99,2);

         add_action('softx_dokan_product_edit_after_title',[$this, 'softx_fields_show_on_edit_page'],99,2);
        // showing on single product page
        //add_action('woocommerce_single_product_summary','show_product_code',13);


    }





public function brands_tax_field(){ ?>

    <div class="dokan-form-group">

    <?php
    $selected_cat  = dokan_posted_input( 'brands' ); 
    $category_args =  array(
        'show_option_none' => __( '- Select a Brand -', 'dokan-lite' ),
        'hierarchical'     => 1,
        'hide_empty'       => 0,
        'name'             => 'brands',
        'id'               => 'brands',
        'required'         => 'required',
        'taxonomy'         => 'brands',
        'title_li'         => '',
        'class'            => 'brands dokan-form-control dokan-select2',
        'exclude'          => '',
        'selected'         => $selected_cat,
    );

    wp_dropdown_categories( apply_filters( 'dokan_product_cat_dropdown_args', $category_args ) );
    ?>
    </div>

   <?php
}



    public function save_add_product_meta($product_id, $postdata){

        if ( ! dokan_is_user_seller( get_current_user_id() ) ) {
            return;
        }
/*
        if ( 
            ! empty( $postdata['brands'] ) && dokan_get_option( 'product_category_style', 'dokan_selling', 'single' ) == 'single' ) {
            wp_set_object_terms( $product_id, (int) $postdata['brands'], 'brands' );
        }
        */
        /**
         * Prices category 
         * Mehedi
         */
        $prices = intval( $postdata['prices']);
        if ( $prices < 0 ) {
            $errors[] = __( 'Please select a price category', 'dokan-lite' );
        }else{
            /**
             * save price terms for the simple product
             */
            wp_set_object_terms( $product_id, $prices, 'prices' );
        }
        /**
         * mehedi
         * save sale product on public 
         */
        if(isset( $postdata['_is_public_product_checkbox'])){ 

            $terms=['private-shop'];

            if($postdata['_is_public_product_checkbox'] === 'yes'){ 
                array_push( $terms,'public-shop');
            }
            wp_set_object_terms( $product_id, $terms, 'shops' );

            update_post_meta( $product_id, '_is_public_product_checkbox', ( $postdata['_is_public_product_checkbox'] === 'yes' ) ? $postdata['_is_public_product_checkbox'] : 'no');
        }

        /**
         * mehedi
         * save public price field meta data
         * 
         */

        if(isset( $postdata['_public_product_price_field']) && isset( $postdata['_is_public_product_checkbox'])){ 
            update_post_meta( $product_id, '_public_product_price_field', ( $postdata['_public_product_price_field'] === '') ? '' : wc_format_decimal( $postdata['_public_product_price_field']));
        }
        

       

    }



    public function show_on_edit_page($post, $post_id){ ?>
        
        <div class="dokan-form-group">
        <?php
    //    $selected_cat  = dokan_posted_input( 'brands' ); 

        $prices = -1;
        $term = array();
        $term = wp_get_post_terms( $post_id, 'brands', array( 'fields' => 'ids') );
        if ( $term ) {
            $prices = reset( $term );
        }
        
        $category_args =  array(
            'show_option_none' => __( '- Select a Fire -', 'dokan-lite' ),
            'hierarchical'     => 1,
            'hide_empty'       => 0,
            'name'             => 'brands',
            'id'               => 'brands',
            'required'         => 'required',
            'taxonomy'         => 'brands',
            'title_li'         => '',
            'class'            => 'brands dokan-form-control dokan-select2',
            'exclude'          => '',
            'selected'         => $prices,
        );

        wp_dropdown_categories( apply_filters( 'dokan_product_cat_dropdown_args', $category_args ) );
        ?>
        </div>

    <?php
    }


/*
    public function show_product_code(){
      global $product;

        if ( empty( $product ) ) {
            return;
        }
            $new_field = get_post_meta( $product->get_id(), 'new_field', true );

        if ( ! empty( $new_field ) ) {
            ?>
                  <span class="details"><?php echo esc_attr__( 'Product Code:', 'dokan-lite' ); ?> <strong><?php echo esc_attr( $new_field ); ?></strong></span>
            <?php
        }
    }

*/

    // dokan plugin new product override

    public function softx_fields_show_on_edit_page($post, $post_id){ ?>

        <div class="dokan-form-group">
            <label for="prices" class="form-label"><?php esc_html_e( 'Price category', 'dokan-lite' ); ?></label>
            <?php
            $prices = -1;
            $term = array();
            $term = wp_get_post_terms( $post_id, 'prices', array( 'fields' => 'ids') );

            if ( $term ) {
                $prices = reset( $term );
            }

            $category_args =  array(
                'show_option_none' => __( '- Select a price category -', 'dokan-lite' ),
                'hierarchical'     => 1,
                'hide_empty'       => 0,
                'name'             => 'prices',
                'id'               => 'prices',
                'taxonomy'         => 'prices',
                'title_li'         => '',
                'class'            => 'prices dokan-form-control dokan-select2',
                'exclude'          => '',
                'selected'         => $prices,

            );

            wp_dropdown_categories( apply_filters( 'dokan_product_cat_dropdown_args', $category_args ) );
            ?>
            <div class="dokan-product-cat-alert dokan-hide">
                <?php esc_html_e('Please choose a price category!', 'dokan-lite' ); ?>
            </div>
        </div>

        <?php dokan_post_input_box( 
                $post_id, 
                '_is_public_product_checkbox', 
                array( 
                    'class' => 'dokan-public-price', 
                    'placeholder' => __( '0.00', 'dokan-lite' ),
                    'label' => __('Is public product')
                    ), 'checkbox' ); 
        ?>
<!--
<div id="public_product_price" class="dokan-form-group
        <?php /*	echo (get_post_meta( $post_id, '_is_public_product_checkbox', true ) == 'yes' ) ?"": 'dokan-hide' */?>
        ">
    <p> <?php esc_html_e('public price', 'dokan-lite'); ?> :   <?php // echo get_post_meta( $post_id, '_is_public_product_checkbox', true ); ?></p>
<?php dokan_post_input_box( $post_id, '_public_product_price_field', array( 'class' => 'dokan-product-public-price', 'placeholder' => __( '0.00', 'dokan-lite' ) ), 'price' ); ?>
</div> -->
<!-- 
    **Mehedi
    ** Hide Price container from directly update 
    
    -->

<?php
    } 




	public function softx_add_fields_after_title(){ ?>

        <div class="dokan-form-group">

            <?php
            $selected_cat  = dokan_posted_input( 'prices' ); 
            $category_args =  array(
                'show_option_none' => __( '- Select a Price category -', 'dokan-lite' ),
                'hierarchical'     => 1,
                'hide_empty'       => 0,
                'name'             => 'prices',
                'id'               => 'prices',
                'required'         => 'required',
                'taxonomy'         => 'prices',
                'title_li'         => '',
                'class'            => 'prices dokan-form-control dokan-select2',
                'exclude'          => '',
                'selected'         => $selected_cat,
            );

            wp_dropdown_categories( apply_filters( 'dokan_product_cat_dropdown_args', $category_args ) );
            ?>
        </div>
<!--
        <div class="dokan-form-group is_public_product">
            <label for="_is_public_product_checkbox"><?php esc_html_e('Product also sell on:')?>  <input type="checkbox" name="_is_public_product_checkbox" id="_is_public_product_checkbox" value="yes"> public 
            </label>
        </div>
        <div id="public_product_price" class="dokan-form-group dokan-hide ">
        <input class="dokan-form-control" name="_public_product_price_field" id="_public_product_price_field" type="number" placeholder="<?php esc_attr_e( 'Public Price..', 'dokan-lite' ); ?>" value="<?php echo esc_attr( dokan_posted_input( '_public_product_price_field' ) ); ?>">
        </div>
-->
    <?php 
	}



  

}
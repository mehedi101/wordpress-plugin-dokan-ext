<?php
namespace Softx;

class Installer
{
    
    public function run(){ 

        // call all the funcation and action to register and initiate all the functionality;
     
        $this->create_custom_roles(); 
     //   $this->create_tables();

    }

    public function create_tables() {
        include_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $this->create_orderformdata_table();
        $this->create_orderinfo_table();

    }

     /**
     * Create order orderformdata table
     *
     * @return void
     */
    public function create_orderformdata_table() {

        global $wpdb;
        $table_name = $wpdb->prefix . "st_orderformdata"; 
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
          `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          `company` varchar(255) NOT NULL,
          `address` text NOT NULL,
          `contact_person` varchar(255) NOT NULL,
          `email` varchar(255) NOT NULL,
          `direct_phone` varchar(255) NOT NULL,
          `ean_number` varchar(255) DEFAULT NULL,
          `upload_file` varchar(255) DEFAULT NULL
          PRIMARY KEY (`id`)
        ) $charset_collate;";

        dbDelta( $sql );
    }

    /**
     * Create order sync table
     *
     * @return void
     */
    public function create_orderinfo_table() {
        global $wpdb;

        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}st_orderinfo` (
          `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          `company_id` bigint(20) DEFAULT NULL,
          `number_of_gift` int(11) NOT NULL,
          `price_category` varchar(255) NOT NULL,
          `delivery_type` varchar(255) NOT NULL,
          `delivery_amount` float NOT NULL,
          `total_amount` float NOT NULL,
          `discount_amount` float NOT NULL,
          `expire_date` date DEFAULT NULL,
          `receiveGift` varchar(255) DEFAULT NULL
          PRIMARY KEY (`id`),
          KEY `company_id` (`company_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        dbDelta( $sql );
    }


    /**
     * Create custom roles
     *
     * @return void
     */
    public function create_custom_roles()
    {
        global $wp_roles;

        if ( class_exists( 'WP_Roles' ) && ! isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles(); // @codingStandardsIgnoreLine
        }

            $roles = $this->role_list();

            foreach($roles as $role){ 

               if( empty( get_role($role['display_name']) )){  
                   add_role(
                       $role['name'],
                       __($role['display_name']),
                       $role['args']
                    );
               }
             }
            $company = get_role('company');
            if( null !== $company){ 
                $company->add_cap('manage_company');
                $company->add_cap('manage_employee');
            } 
    }


    /**
     * list of roles and capabilities
     *
     * @return Array
     */
    public function role_list(){ 

        $Default_cap = [
            'read' => true,
            'edit_post' => true,
            'employee' => true,

        ];

        return [
           /*  [
                'name' => '150dkk',
                'display_name' =>__('150DKK','softx-dokan'),
                'args' => $Default_cap
            ], */
            [
                'name' => '200dkk',
                'display_name' =>__('200DKK','softx-dokan'),
                'args' => $Default_cap
            ],
            [
                'name' => '300dkk',
                'display_name' =>__('300DKK','softx-dokan'),
                'args' => $Default_cap
            ],
            [
                'name' => '500dkk',
                'display_name' =>__('500DKK','softx-dokan'),
                'args' => $Default_cap
            ],
            [
                'name' => '800dkk',
                'display_name' =>__('800DKK','softx-dokan'),
                'args' => $Default_cap
            ],
            [
                'name' => '1200dkk',
                'display_name' =>__('1200DKK','softx-dokan'),
                'args' => $Default_cap
            ],
            [
                'name' => 'company',
                'display_name' =>__('Company','softx-dokan'),
                'args' => $Default_cap
            ]
        ];

        

        
    
    }

}
<?php
namespace Softx;

class Installer
{
    
    public function run(){ 

        // call all the funcation and action to register and initiate all the functionality;
     
        $this->create_custom_roles(); 


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
            [
                'name' => '150dkk',
                'display_name' =>'150DKK',
                'args' => $Default_cap
            ],
            [
                'name' => '200dkk',
                'display_name' =>'200DKK',
                'args' => $Default_cap
            ],
            [
                'name' => '300dkk',
                'display_name' =>'300DKK',
                'args' => $Default_cap
            ],
            [
                'name' => '500dkk',
                'display_name' =>'500DKK',
                'args' => $Default_cap
            ],
            [
                'name' => '800dkk',
                'display_name' =>'800DKK',
                'args' => $Default_cap
            ],
            [
                'name' => 'company',
                'display_name' =>'Company',
                'args' => $Default_cap
            ],
        ];

        

        
    
    }

}
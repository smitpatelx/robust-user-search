<?php
/*
Plugin Name: Robust User Search
Description: Search users easily with this supercharged plugin.
Plugin URI:  https://smitpatelx.com
Author:      Smit Patel
Author URI:  https://smitpatelx.com
Version:     1.0
License:     GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.txt
*/

// Adding plugin page to WooCommerce SubMenu
function rus_register_menu_page() { 
    // add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', string $icon_url = '', int $position = null ), 
    add_menu_page( 'Robust user search', 'Robust Search', 'manage_options', 'rus', 'rus_display_callback', 'dashicons-superhero', 25); 
}

// Page callback function
function rus_display_callback() {
    $dir = plugin_dir_path(__FILE__);
    wp_enqueue_style( 'rus-css', '/wp-content/plugins/robust-user-search/dist/css/app.css' , array(), null, false);
    wp_enqueue_script( 'rus-manifest', '/wp-content/plugins/robust-user-search/dist/js/manifest.js', array(), null, true);
    wp_enqueue_script( 'rus-vendor', '/wp-content/plugins/robust-user-search/dist/js/vendor.js', array(), null, true);
    wp_enqueue_script( 'rus-app', '/wp-content/plugins/robust-user-search/dist/js/app.js', array(), null, true);
    wp_localize_script('rus-app', 'rusN', array(
        'rootapiurl' => esc_url_raw(rest_url()),
        'nonce' => wp_create_nonce('wp_rest')
    ));
    
    ?>
    <div class="flex flex-wrap pr-4 pl-2 py-4 antialiased" style="width:100% !important;">
        <div class="flex flex-wrap w-full items-center justify-center">
            <span style="margin-bottom:0.2rem;font-weight:500;font-size:1.2rem !important;" class="text-gray-500 py-2 rounded-full">
                Robust User Search
            </span>
            <span class="dashicons dashicons-superhero fill-current text-gray-600 mb-1 ml-3"></span>
        </div>
        <div class="w-full flex flex-wrap mt-2">
            <div id="vueApp" class="w-full">
                <app-layout/>
            </div>
        </div>
        <div class="flex flex-wrap items-center justify-center absolute top-0 right-0 p-3"> 
            <a href="https://smitpatelx.com" target="_blank" class="text-base text-gray-500 underline">By: Smit Patel</a>
        </div>
    </div>
    <style>
        .error{
            display:none !important;
        }
        #wpfooter{
            display: none !important;
        }
    </style>
    <?php
}
add_action('admin_menu', 'rus_register_menu_page', 99);

// Custom End-point
add_action( 'rest_api_init', function () {
    register_rest_route( 'rsu/v1', '/all', array(
        'methods' => 'GET',
        'callback' => 'rus_return_data',
        'permission_callback' => function($request){	  
            return current_user_can('manage_options');
        }
    ));
    
    register_rest_route( 'rsu/v1', '/roles', array(
        'methods' => 'GET',
        'callback' => 'rus_get_all_roles',
        'permission_callback' => function($request){	  
            return current_user_can('manage_options');
        }
    ));
} );

function rus_return_data(WP_REST_Request $request){
    extract($request->get_params());
    $DBRecord = array();
    $args = array(
        'orderby' => 'first_name',
        'order'   => 'ASC'
    );
    if($role){
        $args = array(
            'orderby' => 'first_name',
            'order'   => 'ASC',
            'role'    => $role
        );
    }
    $users = get_users( $args );
    $i=0;
    // return $users;
    foreach ( $users as $user )
    {
        // print_r[$user];
        $DBRecord[$i]['roles']                  = $user->roles;   
        $DBRecord[$i]['username']               = $user->user_login;   
        $DBRecord[$i]['id']                     = $user->ID;  
        $DBRecord[$i]['first_name']             = $user->first_name;
        $DBRecord[$i]['last_name']              = $user->last_name;
        $DBRecord[$i]['user_registered']        = $user->user_registered;
        $DBRecord[$i]['email']                  = $user->user_email;

        $UserData = get_user_meta( $user->ID );  
        $DBRecord[$i]['billing_company']        = $UserData['billing_company'][0];    
        $DBRecord[$i]['billing_address_1']      = $UserData['billing_address_1'][0];
        $DBRecord[$i]['billing_city']           = $UserData['billing_city'][0];
        $DBRecord[$i]['billing_state']          = $UserData['billing_state'][0];
        $DBRecord[$i]['billing_postcode']       = $UserData['billing_postcode'][0];
        $DBRecord[$i]['billing_country']        = $UserData['billing_country'][0];
        $DBRecord[$i]['billing_phone']          = $UserData['billing_phone'][0];
        $i++;
    }
    return new WP_REST_Response($DBRecord, 200);
}

//List all roles
function rus_get_all_roles() {
    global $wp_roles;

    $all_roles = $wp_roles->roles;
    $editable_roles = apply_filters('editable_roles', $all_roles);

    return $editable_roles;
}
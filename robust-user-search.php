<?php
/*
Plugin Name: Robust User Search
Description: Search users easily with this supercharged plugin.
Plugin URI:  https://smitpatelx.com/robust-user-search
Author:      Smit Patel
Author URI:  https://smitpatelx.com
Version:     1.0
License:     GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.txt
*/

// Adding plugin page to WooCommerce SubMenu
function rus_register_menu_page() { 
    // add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', string $icon_url = '', int $position = null ), 
    add_menu_page( 'Robust user search', 'Robust Search', 'manage_options', 'rus', 'rus_display_callback', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAJmSURBVHgBzVXdcdpAEN6VRJ7sMXRAOsBvGU8SKxVAKjCpAKcCoAKcCgwVBFdghXgyfgsdWKkADH4C6TbfciKWFMRPZjLjnZHmfna/b29vd4/oPwvvo+TLpGzIa2F4CZMpkfQXFA3uuRL+M4GCxuQ0iZw6lPxNOkIU4D9wKR4GXJnuRfBW5r5DXIdhE9NyAgVjHhiioc4cuwcdTvZpKsRDAdkdHweFBO/l6QFg1ayH5sYl0897aE/oNgBxkT0hhyM+el1AMJdk2HUouio6dl5AVsUdNTFs63zEx5tDrwQpko3yBmBFe5vsHTpAEMLWK/J+HmKzN4G/8lw6cG9MB8heBDbGpVsh02WSRzpAvF0KtsgUnL7oHOl48k5mzfW+UBzecSUost95AmRHW4vpOx9dIS0DJvMNp6jaj3sOef42+60EyIi2LTiD8WPHo7gRkTsc8UkHppotA6RkZxtGJkTwKkQIqlrNWpGGInjr/nouF75AW5gim85B2nQoPl3bnsmklgzHhQQo9RuAtBwyPqZBPrbnMq+b1chcLij+cJ8qxBKVWqIIxBmCTIgMOcNkubWpoABeRtzbCXi4XtcsA3RDx0tadgsJNCxiG1q5RN51ngCBqgk53Xybjq1uGd7383t/XbJL0WcEK9QGhlj3ssrRqWZTeg2J0LPNTsK894VyJk81GE5sb8mSpOVcZtdWZzZJXXJGeBuJR3JLqzeBwwUt/8Q9qeyv8Lqmb0WEO/nBlfFBBM9ALki4SqtHRVuFK9qTLLGEAP9YBL6TIEXS0RpIr2sy4L4+7ftm7BTtP4j1g35aiPRS5Dc2UTIXCVE7mwAAAABJRU5ErkJggg==', 25); 
    // add_action( 'admin_print_styles-' . $menu, 'cake_css' );
}
// Style for icon
add_action('admin_head', 'rus_custom_favicon');
function rus_custom_favicon() {
  echo "
    <style>
    .toplevel_page_rus img{
        margin-top:0px !important;
        padding-top:5px !important;
    }
    </style>"; 
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
    <div class="flex flex-wrap antialiased" style="width:100% !important;">
        <div class="w-full flex flex-wrap mt-2">
            <div id="vueApp" class="w-full">
                <app-layout/>
            </div>
        </div>
        
    </div>
    <style>
        .error, .settings-error{
            display:none !important;
        }
        #wpfooter{
            display: none !important;
        }
        body{
            background: #fff !important;
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

    //Helper Function
    function filter_null($val){
        if($val===NULL) {
            return "";
        } else {
            return $val;
        }
    }

    foreach ( $users as $user )
    {
        // print_r[$user];
        $DBRecord[$i]['roles']                  = filter_null($user->roles);
        $DBRecord[$i]['username']               = filter_null($user->user_login);
        $DBRecord[$i]['id']                     = filter_null($user->ID);
        $DBRecord[$i]['first_name']             = filter_null($user->first_name);
        $DBRecord[$i]['last_name']              = filter_null($user->last_name);
        $DBRecord[$i]['user_registered']        = filter_null($user->user_registered);
        $DBRecord[$i]['email']                  = filter_null($user->user_email);

        $UserData = get_user_meta( $user->ID );  
        $DBRecord[$i]['billing_company']        = filter_null($UserData['billing_company'][0]);
        $DBRecord[$i]['billing_address_1']      = filter_null($UserData['billing_address_1'][0]);
        $DBRecord[$i]['billing_city']           = filter_null($UserData['billing_city'][0]);
        $DBRecord[$i]['billing_state']          = filter_null($UserData['billing_state'][0]);
        $DBRecord[$i]['billing_postcode']       = filter_null($UserData['billing_postcode'][0]);
        $DBRecord[$i]['billing_country']        = filter_null($UserData['billing_country'][0]);
        $DBRecord[$i]['billing_phone']          = filter_null($UserData['billing_phone'][0]);
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
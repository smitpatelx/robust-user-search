<?php
/*
Plugin Name: Robust User Search
Description: Search users easily with this supercharged plugin.
Plugin URI:  https://smitpatelx.com/robust-user-search
Author:      Smit Patel
Author URI:  https://smitpatelx.com
Version:     1.0.2
License:     GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.txt
*/
require(__DIR__.'/api/list-all-users.php');
require(__DIR__.'/api/list-single-user.php');
require(__DIR__.'/api/list-all-roles.php');
require(__DIR__.'/api/edit-single-user.php');

// Adding plugin page to WooCommerce SubMenu
function rus_register_menu_page() { 
    // Create custom role - robust_user_search
    $admin_role = get_role('administrator');
    $shop_manager_role = get_role('shop_manager');
    $admin_role->add_cap( 'robust_user_search', true );
    $shop_manager_role->add_cap( 'robust_user_search', true );

    // add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', string $icon_url = '', int $position = null ), 
    add_menu_page( 'Robust user search', 'Robust Search', 'robust_user_search', 'rus', 'rus_display_callback', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAJmSURBVHgBzVXdcdpAEN6VRJ7sMXRAOsBvGU8SKxVAKjCpAKcCoAKcCgwVBFdghXgyfgsdWKkADH4C6TbfciKWFMRPZjLjnZHmfna/b29vd4/oPwvvo+TLpGzIa2F4CZMpkfQXFA3uuRL+M4GCxuQ0iZw6lPxNOkIU4D9wKR4GXJnuRfBW5r5DXIdhE9NyAgVjHhiioc4cuwcdTvZpKsRDAdkdHweFBO/l6QFg1ayH5sYl0897aE/oNgBxkT0hhyM+el1AMJdk2HUouio6dl5AVsUdNTFs63zEx5tDrwQpko3yBmBFe5vsHTpAEMLWK/J+HmKzN4G/8lw6cG9MB8heBDbGpVsh02WSRzpAvF0KtsgUnL7oHOl48k5mzfW+UBzecSUost95AmRHW4vpOx9dIS0DJvMNp6jaj3sOef42+60EyIi2LTiD8WPHo7gRkTsc8UkHppotA6RkZxtGJkTwKkQIqlrNWpGGInjr/nouF75AW5gim85B2nQoPl3bnsmklgzHhQQo9RuAtBwyPqZBPrbnMq+b1chcLij+cJ8qxBKVWqIIxBmCTIgMOcNkubWpoABeRtzbCXi4XtcsA3RDx0tadgsJNCxiG1q5RN51ngCBqgk53Xybjq1uGd7383t/XbJL0WcEK9QGhlj3ssrRqWZTeg2J0LPNTsK894VyJk81GE5sb8mSpOVcZtdWZzZJXXJGeBuJR3JLqzeBwwUt/8Q9qeyv8Lqmb0WEO/nBlfFBBM9ALki4SqtHRVuFK9qTLLGEAP9YBL6TIEXS0RpIr2sy4L4+7ftm7BTtP4j1g35aiPRS5Dc2UTIXCVE7mwAAAABJRU5ErkJggg==', 25); 
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
    $dir = explode("/", plugin_basename(__FILE__))[0];
    wp_enqueue_style( 'rus-css', '/wp-content/plugins/'.$dir.'/dist/css/app.css' , array(), null, false);
    wp_enqueue_script( 'rus-manifest', '/wp-content/plugins/'.$dir.'/dist/js/manifest.js', array(), null, true);
    wp_enqueue_script( 'rus-vendor', '/wp-content/plugins/'.$dir.'/dist/js/vendor.js', array(), null, true);
    wp_enqueue_script( 'rus-app', '/wp-content/plugins/'.$dir.'/dist/js/app.js', array(), null, true);
    wp_localize_script('rus-app', 'rusN', array(
        'rootapiurl' => esc_url_raw(rest_url()),
        'nonce' => wp_create_nonce('wp_rest')
    ));
    
    ?>
    <div class="flex flex-wrap antialiased relative" style="width:100% !important;">
        <div class="w-full flex flex-wrap mt-2">
            <div id="vueApp" class="w-full">
                <app-layout/>
            </div>
        </div>
    </div>
    <style>
        .error, .settings-error, .notice{
            display:none !important;
        }
        #wpfooter{
            display: none !important;
        }
        body{
            background: #ffff !important;
        }
    </style>
    <?php
}
add_action('admin_menu', 'rus_register_menu_page', 99);

/*
 * Custom End-point Definiton
 * Routes       : rus/v1/all
 *                rus/v1/roles
 */
add_action( 'rest_api_init', function () {
    register_rest_route( 'rsu/v1', '/all', array(
        'methods' => 'GET',
        'callback' => 'rus_get_all_users',
        'permission_callback' => function($request){	  
            return current_user_can('robust_user_search');
        }
    ));

    register_rest_route( 'rsu/v1', '/user/(?P<id>\d+)', array(
        'methods' => 'PUT',
        'callback' => 'rus_edit_user',
        'permission_callback' => function($request){	  
            return current_user_can('robust_user_search');
        }
    ));

    register_rest_route( 'rsu/v1', '/user/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'rus_get_single_user',
        'permission_callback' => function($request){	  
            return current_user_can('robust_user_search');
        }
    ));
    
    register_rest_route( 'rsu/v1', '/roles', array(
        'methods' => 'GET',
        'callback' => 'rus_get_all_roles',
        'permission_callback' => function($request){	  
            return current_user_can('robust_user_search');
        }
    ));
} );
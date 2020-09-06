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

if(!defined('ABSPATH')){
    exit;//Exit if accessed directly
}

include_once(__DIR__.'/rus_includes/activation.php');
include_once(__DIR__.'/rus_includes/deactivate.php');
include_once(__DIR__.'/rus_includes/menu-page.php');
include_once(__DIR__.'/rus_includes/settings-page.php');
include_once(__DIR__.'/api/list-all-users.php');
include_once(__DIR__.'/api/list-single-user.php');
include_once(__DIR__.'/api/list-all-roles.php');
include_once(__DIR__.'/api/edit-single-user.php');
include_once(__DIR__.'/api/edit-single-user.php');

register_activation_hook( __FILE__, 'rus_activation');
register_deactivation_hook( __FILE__, 'rus_deactivation');

// Register Pages
add_action('admin_menu', 'rus_register_main_page', 99);
add_action('admin_menu', 'rus_register_settings_page', 99);

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
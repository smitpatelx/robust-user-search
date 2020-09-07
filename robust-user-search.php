<?php
/**
 * Plugin Name:         Robust User Search
 * Description:         Search users easily with this supercharged plugin.
 * Plugin URI:          https://wordpress.org/plugins/robust-user-search
 * Text Domain:         robust-user-search
 * Author:              Smit Patel
 * Author URI:          https://smitpatelx.com
 * Version:             1.0.4
 * Requires at least:   5.2
 * Requires PHP:        7.1
 * License:             MIT
 * License URI:         https://github.com/smitpatelx/robust_user_search/blob/master/LICENSE
 */

/**
 * Robust User Search Main Class
 * 
 * @package    robust-user-search
 * @author     Smit Patel <smitpatel.dev@gmail.com>
 */
class RobustUserSearch {
    
    /**
     * Security Check & call required functions
     *
     * @param null
     * @return null
     */
    public function __construct(){
        $this->checkSecurity();

        require_once(__DIR__."/constants.php");
        new Constants();

        $this->checkWpVersion();
        $this->includingFile();
        $this->registerHooks();
        $this->registerAllPages();
        $this->registerRestApi();
    }

    /**
     * Check if the constant ABSPATH is defined
     *
     * @param null
     * @return null
     */
    public static function checkSecurity(){
        defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' );
    }

    /**
     * Check required wordpress version
     * 
     * @param none
     * @return none
     */
    protected function checkWpVersion(){
        if(!empty(WP_CURRENT_VERSION) && version_compare( WP_CURRENT_VERSION, RUS_MINIMUM_WP_REQUIRED_VERSION, '<')){
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error is-dismissible">';
                echo '<p>Robust User Search only supports Wordpress version greater than '.RUS_MINIMUM_WP_REQUIRED_VERSION.' , Your current version is :<b>'.$wp_version.'</b></p>';
                echo '</div>';
            });
            die;
        }
    }

    /**
     * Include all files
     * 
     * @param none
     * @return none
     */
    protected function includingFile(){
        require_once(RUS_DIRECTORY.'/includes/activation.php');
        require_once(RUS_DIRECTORY.'/includes/deactivate.php');
        require_once(RUS_DIRECTORY.'/includes/index-controller.php');
        require_once(RUS_DIRECTORY.'/includes/settings-controller.php');

        include_once(RUS_DIRECTORY.'/api/list-all-users.php');
        include_once(RUS_DIRECTORY.'/api/list-single-user.php');
        include_once(RUS_DIRECTORY.'/api/list-all-roles.php');
        include_once(RUS_DIRECTORY.'/api/edit-single-user.php');
    }

    /**
     * Register activation and deactivation hooks
     * 
     * @param none
     * @return none
     */
    protected function registerHooks(){
        new Activation(__FILE__);
        new Deactivation(__FILE__);
    }

    /**
     * Add action to register pages into admin menu
     * 
     * @param none
     * @return none
     */
    protected function registerAllPages(){
        add_action('admin_head', ['IndexController', 'customFavicon']);
        add_action('admin_menu', ['IndexController', 'instance'], 99);
        add_action('admin_menu', ['SettingsController', 'instance'], 99);
    }

    /**
     * Calls to register rest APIs
     * 
     * @param none
     * @return none
     */
    protected function registerRestApi(){
        add_action( 'rest_api_init', function () {
            new RestApi_GetAllUsers();

            new RestApi_PutEditUser();

            new RestApi_GetSingleUser();
            
            new RestApi_GetRoles();
        });
    }

}

new RobustUserSearch();
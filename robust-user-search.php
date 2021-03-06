<?php
/**
 * Plugin Name:         Robust User Search
 * Description:         Search users easily with this supercharged plugin.
 * Plugin URI:          https://smitpatelx.com/apps/robust-user-search
 * Text Domain:         robust-user-search
 * Author:              Smit Patel
 * Author URI:          https://smitpatelx.com
 * Version:             1.0.5
 * Requires at least:   5.2
 * Requires PHP:        7.1
 * License:             gpl-v2-only
 * License URI:         https://github.com/smitpatelx/robust_user_search/blob/master/LICENSE
 */
namespace Rus;

use Rus\Helper\RusHelper;

use Rus\Includes\RusActivation;
use Rus\Includes\RusDeactivation;

use Rus\Api\RusRestApiGetAllUsers;
use Rus\Api\RusRestApiPutEditUser;
use Rus\Api\RusRestApiGetSingleUser;
use Rus\Api\RusRestApiGetRoles;
/**
 * Robust User Search Main Class
 * 
 * @package    robust-user-search
 * @author     Smit Patel <smitpatel.dev@gmail.com>
 */
class Rus {
    
    /**
     * Security Check & call required functions
     *
     * @param null
     * @return null
     */
    public function __construct(){
        $this->includingFile();
        
        RusHelper::checkSecurity();
        new Constants();

        $this->checkWpVersion();
        $this->registerHooks();
        $this->registerAllPages();
        $this->registerRestApi();
    }

    /**
     * Check required wordpress version
     * 
     * @param none
     * @return none
     */
    protected function checkWpVersion(){
        if(!empty(RUS_WP_CURRENT_VERSION) && version_compare( RUS_WP_CURRENT_VERSION, RUS_MINIMUM_WP_REQUIRED_VERSION, '<')){
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
        require_once(__DIR__."/constants.php");

        require_once(__DIR__.'/helper/Helper.php');
        require_once(__DIR__.'/includes/activation.php');
        require_once(__DIR__.'/includes/deactivate.php');
        require_once(__DIR__.'/includes/index-controller.php');
        require_once(__DIR__.'/includes/settings-controller.php');

        include_once(__DIR__.'/api/list-all-users.php');
        include_once(__DIR__.'/api/list-single-user.php');
        include_once(__DIR__.'/api/list-all-roles.php');
        include_once(__DIR__.'/api/edit-single-user.php');
    }

    /**
     * Register activation and deactivation hooks
     * 
     * @param none
     * @return none
     */
    protected function registerHooks(){
        new RusActivation(__FILE__);
        new RusDeactivation(__FILE__);
    }

    /**
     * Add action to register pages into admin menu
     * 
     * @param none
     * @return none
     */
    protected function registerAllPages(){
        add_action('admin_head', ['Rus\Includes\RusIndexController', 'customFavicon']);
        add_action('admin_menu', ['Rus\Includes\RusIndexController', 'instance'], 99);
        add_action('admin_menu', ['Rus\Includes\RusSettingsController', 'instance'], 99);
    }

    /**
     * Calls to register rest APIs
     * 
     * @param none
     * @return none
     */
    protected function registerRestApi(){
        add_action( 'rest_api_init', function () {
            new RusRestApiGetAllUsers();

            new RusRestApiPutEditUser();

            new RusRestApiGetSingleUser();
            
            new RusRestApiGetRoles();
        });
    }

}

new Rus();
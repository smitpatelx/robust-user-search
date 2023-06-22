<?php
/**
 * Plugin Name:         Robust User Search
 * Description:         Search users easily with this supercharged plugin.
 * Plugin URI:          https://smitpatelx.com/apps/robust-user-search
 * Text Domain:         robust-user-search
 * Author:              Smit Patel
 * Author URI:          https://smitpatelx.com
 * Version:             1.1.0
 * Requires at least:   5.2
 * Requires PHP:        7.1
 * License:             gpl-v2-only
 * License URI:         https://github.com/smitpatelx/robust_user_search/blob/main/LICENSE
 */
namespace Rus;

use Rus\Helper\RusHelper;

use Rus\Includes\RusActivation;
use Rus\Includes\RusDeactivation;
use Rus\Controller\RusUser;

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
        new RusUser();

        $this->checkWpVersion();
        $this->registerHooks();
        $this->registerAllPages();
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
        require_once(__DIR__.'/helper/Validation.php');
        require_once(__DIR__.'/includes/activation.php');
        require_once(__DIR__.'/includes/deactivate.php');

        require_once(__DIR__.'/controller/user.php');
        require_once(__DIR__.'/controller/index.php');
        require_once(__DIR__.'/controller/settings.php');
    }

    /**
     * Register activation and deactivation hooks
     * 
     * @param none
     * @return none
     */
    protected function registerHooks(){
        new RusActivation();
        new RusDeactivation();
    }

    /**
     * Add action to register pages into admin menu
     * 
     * @param none
     * @return none
     */
    protected function registerAllPages(){
        add_action('admin_menu', ['Rus\Controller\RusIndex', 'instance'], 99);
        add_action('admin_menu', ['Rus\Controller\RusSetting', 'instance'], 99);
    }
}

new Rus();
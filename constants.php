<?php
namespace Rus;

use Rus\Helper\RusHelper;   
/**
 * Robust User Search Main Class
 * 
 * @package    robust-user-search
 * @author     Smit Patel <smitpatel.dev@gmail.com>
 */
class Constants {

    public function __construct(){
        RusHelper::checkSecurity();
        $this->define_rus_constants();
    }

    private function define_rus_constants(){
        global $wp_version;

        define('RUS_MINIMUM_WP_REQUIRED_VERSION', 5.2);
        define('RUS_DIRECTORY', __DIR__);
        define('RUS_WP_CURRENT_VERSION', $wp_version);
        define('RUS_CAPABILITY', 'robust_user_search');
        define('RUS_ADMIN_CAPABILITY', 'manage_options');
        define('RUS_MENU_ICON_URL', plugins_url('/assets/robust_teal.svg', __FILE__));
        define('RUS_FAVICON_URL', plugins_url('/dist/favicon.ico', __FILE__));

        define('RUS_DIST_CSS_APP', plugins_url('/dist/assets/index.css', __FILE__));
        define('RUS_DIST_JS_APP', plugins_url('/dist/assets/index.js', __FILE__));
    }
}

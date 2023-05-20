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
        define('RUS_FILE_PATH', __FILE__);
        define('RUS_WP_CURRENT_VERSION', $wp_version);
        define('RUS_CAPABILITY', 'robust_user_search');
        define('RUS_MENU_ICON_URL', plugins_url('assets/robust_teal.svg', __FILE__));

        define('RUS_DIST_CSS_APP', plugins_url('/dist/css/app.css', __FILE__));
        define('RUS_DIST_JS_MANIFEST', plugins_url('/dist/js/manifest.js', __FILE__));
        define('RUS_DIST_JS_VENDOR', plugins_url('/dist/js/vendor.js', __FILE__));
        define('RUS_DIST_JS_APP', plugins_url('/dist/js/app.js', __FILE__));
        define('RUS_FONTS', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap');
    }

}

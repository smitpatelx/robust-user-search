<?php
/**
 * Robust User Search Main Class
 * 
 * @package    robust-user-search
 * @author     Smit Patel <smitpatel.dev@gmail.com>
 */
class Constants {

    public function __construct(){
        RobustUserSearch::checkSecurity();
        $this->define_rus_constants();
    }

    private function define_rus_constants(){
        global $wp_version;

        define('RUS_MINIMUM_WP_REQUIRED_VERSION', 5.2);
        define('RUS_DIRECTORY', __DIR__);
        define('WP_CURRENT_VERSION', $wp_version);
    }

}

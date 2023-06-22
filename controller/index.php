<?php
namespace Rus\Controller;

use Rus\Helper\RusHelper;

/**
 * Index Controller to handle home page for this plugin
 * 
 * @package    robust-user-search
 * @subpackage Controller
 * @author     Smit Patel <smitpatel.dev@gmail.com>
 */
class RusIndex {
    private static $instance;

    /**
     * Security Check
     *
     * @param null
     * @return null
     */
    public function __construct(){
        RusHelper::checkSecurity();
    }

    /**
     * Create a new instance and run register function
     *
     * @param null
     * @return null
     */
    public static function instance(){
        $instance = new self;
        $instance->register();
    }

    /**
     * Add specific styling to display icon
     *
     * @param null
     * @return null
     */
    public static function customFavicon() {
        echo "<style>
            .toplevel_page_rus #adminmenu .wp-menu-image img{
                margin-top: 0px !important;
                padding-top: 5px !important;
            }
            </style>"; 
    }

    /**
     * Adding main menu page to wordpress admin
     *
     * @param null
     * @return null
     */
    public function register() { 
        // add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', string $icon_url = '', int $position = null ), 
        add_menu_page(
            'Robust user search',
            'Robust search',
            RUS_CAPABILITY,
            'rus',
            array( $this, 'indexOutput'),
            RUS_MENU_ICON_URL,
            25,
        );
    }


    /**
     * Display output for index page
     *
     * @param null
     * @return null
     */
    public function indexOutput() {
        wp_enqueue_style( 'rus-css', RUS_DIST_CSS_APP, array(), null, false);
        wp_enqueue_script( 'rus-app', RUS_DIST_JS_APP, array(), null, true);
        wp_localize_script('rus-app', 'rusN', array(
            'rootapiurl' => esc_url_raw(rest_url()),
            'nonce' => wp_create_nonce('wp_rest')
        ));
        
        $allowed_html = array(
            'link' => array(
                'href' => array(),
                'rel' => array(),
                'style' => array(),
                'type' => array(),
            ),
            'div' => array(
                'id' => array(),
                'class' => array(),
                'style' => array()
            ),
            'app-layout' => array(),
            'style' => array(),
            'img' => array(
                'src' => array(),
                'alt' => array(),
                'style' => array(),
            )
        );
        echo wp_kses('
        <div class="flex flex-wrap" style="width:100% !important;">
            <div class="w-full flex flex-wrap mt-2">
                <div id="vueApp" class="w-full"/>
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
        ', $allowed_html);
    }
}
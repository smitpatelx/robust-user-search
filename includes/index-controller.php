<?php
namespace Rus\Includes;

use Rus\Helper\RusHelper;
/**
 * Index controller to handle home page for this plugin
 * 
 * @package    robust-user-search
 * @subpackage includes
 * @author     Smit Patel <smitpatel.dev@gmail.com>
 */
class RusIndexController {
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
            .toplevel_page_rus img{
                margin-top:0px !important;
                padding-top:5px !important;
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

        // Icon styling for sidebar menu
        $allowed_html = array(
            'style' => array(),
        );
        echo wp_kses('
        <style>
            :root {
                --rus-icon-height: 2.1rem !important;
                --rus-icon-width: 2.1rem !important;
            }

            #toplevel_page_rus div.wp-menu-image {
                position: relative !important;
                padding: 0px !important;
            }

            #toplevel_page_rus img{
                position: absolute !important;
                top: 50% !important;
                left: 50% !important;
                transform: translate(-50%, -50%) !important;
                height: var(--rus-icon-height);
                min-height: var(--rus-icon-height);
                max-height: var(--rus-icon-height);
                width: var(--rus-icon-width);
                min-width: var(--rus-icon-width);
                max-width: var(--rus-icon-width);
                padding: 0px !important;
                margin: 0.3rem 0rem 0rem 0rem !important;
            }
        </style>
        ', $allowed_html);
    }


    /**
     * Display output for index page
     *
     * @param null
     * @return null
     */
    public function indexOutput() {
        wp_enqueue_style( 'rus-css', RUS_DIST_CSS_APP, array(), null, false);
        wp_enqueue_style( 'rus-fonts', RUS_FONTS, array(), null, false);
        wp_enqueue_script( 'rus-manifest', RUS_DIST_JS_MANIFEST, array(), null, true);
        wp_enqueue_script( 'rus-vendor', RUS_DIST_JS_VENDOR, array(), null, true);
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
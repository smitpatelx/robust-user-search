<?php
namespace Rus\Includes;

use Rus\Helper\RusHelper;
/**
 * Settings controller to handle settings page for this plugin
 * 
 * @package    robust-user-search
 * @subpackage includes
 * @author     Smit Patel <smitpatel.dev@gmail.com>
 */
class RusSettingsController {
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
     * Adding settings submenu page to wordpress admin
     *
     * @param null
     * @return null
     */
    public function register() {
        //add_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', int $position = null )
        add_submenu_page( 'rus', 'Settings', 'Settings', RUS_CAPABILITY, 'rus-settings', array( $this, 'settingsOutput') );
    }

    /**
     * Display output for settings page
     *
     * @param null
     * @return null
     */
    public function settingsOutput(){
        $db_saved_roles = get_option('rus_allowed_roles',[]);
        $current_user_role = wp_get_current_user()->roles[0];

        global $wp_roles;
        $all_roles = $wp_roles->roles;
        $editable_roles = apply_filters('editable_roles', $all_roles);

        wp_enqueue_style( 'rus-css', RUS_DIST_CSS_APP, array(), null, false);
        wp_enqueue_style( 'rus-fonts', RUS_FONTS, array(), null, false);

        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $errors=[];
            $allowed_roles_array = [];

            // To prevent user from submitting new roles
            foreach($editable_roles as $key => $role){
                $current_post_value = isset($_POST[$key]) ? sanitize_text_field($_POST[$key]) : '';
                // Validating $_POST
                if( isset($_POST[$key]) && $key == $current_post_value){
                    array_push($allowed_roles_array, $current_post_value);
                }
            }
            
            if(isset($allowed_roles_array) && !empty($allowed_roles_array)) {
                $update_success = update_option('rus_allowed_roles', $allowed_roles_array);
            } else {
                array_push($errors,"allowed_roles_array must be non-empty array.");
            }

            if($update_success){
                foreach($editable_roles as $key => $role){
                    $get_role = get_role($key);
                    $get_role->remove_cap(RUS_CAPABILITY);
                }
                foreach($allowed_roles_array as $role){
                    $get_role = get_role($role);
                    $get_role->add_cap(RUS_CAPABILITY, true);
                }

                global $wp_roles;
                $all_roles = $wp_roles->roles;
                $editable_roles = apply_filters('editable_roles', $all_roles);
            } else {
                array_push($errors,"Maybe their is no change in data");
            }
        }

        ?>
        <div id="rusSettings" class="flex flex-wrap antialiased relative font-sans" style="width:100% !important;">
            <form method="post" name="form-role" action="" class="w-full flex flex-wrap flex-col mt-2 p-4">
                <p class="text-2xl text-gray-800 font-medium flex flex-wrap items-center justify-center">
                    Settings
                    <svg class="h-8 w-8 inline-block ml-3" viewBox="0 0 110 110"><path fill="url(#paint0_linear)" fill-rule="evenodd" d="M53.4 4.9c1-.4 2.2-.4 3.2 0l36.7 13.7c1.8.7 3 2.4 3 4.3V55c0 15.8-10.5 28.4-20 36.7a104 104 0 01-19.1 13.2H57l-2.1-4-2 4h-.1a27.3 27.3 0 01-1.7-.9 97.3 97.3 0 01-17.6-12.3C24.2 83.4 13.7 70.8 13.7 55V23c0-2 1.2-3.7 3-4.4L53.4 5zm1.6 96l-2 4c1.2.7 2.8.7 4 0l-2-4zm0-5.3a89.9 89.9 0 0015.3-10.8c9-7.8 16.8-18.1 16.8-29.8V26L55 14 23 26v29c0 11.7 7.8 22 16.7 29.8A94.8 94.8 0 0055 95.6z" clip-rule="evenodd"/><path fill="url(#paint1_linear)" fill-rule="evenodd" d="M58 31.3c1 .4 1.4 1.3 1.3 2.3l-1.8 14.9h17a2.2 2.2 0 011.7 3.6L54.5 78a2.2 2.2 0 01-3.8-1.7l1.8-14.9h-17a2.2 2.2 0 01-1.7-3.6l21.7-26c.6-.7 1.6-1 2.5-.6zM40.1 57.2H55a2.2 2.2 0 012.1 2.4l-1.2 10 14-16.8H55a2.2 2.2 0 01-2.1-2.4l1.2-10-14 16.8z" clip-rule="evenodd"/><defs><linearGradient id="paint0_linear" x1="55" x2="55" y1="4.6" y2="105.4" gradientUnits="userSpaceOnUse"><stop offset=".4" stop-color="#0A192F"/><stop offset="1" stop-color="#3EFFF3"/></linearGradient><linearGradient id="paint1_linear" x1="55" x2="55" y1="31.2" y2="78.8" gradientUnits="userSpaceOnUse"><stop offset=".4" stop-color="#0A192F"/><stop offset="1" stop-color="#3EFFF3"/></linearGradient></defs></svg>
                </p>
                    <?php
                        if(isset($errors) && !empty($errors)){
                            echo '<div class="w-full flex flex-wrap flex-col justify-center items-center text-base mt-6 mb-4 pb-3">';
                            foreach($errors as $error){
                                echo "<span class='bg-red-200 border-l-4 border-red-700 text-red-700 py-2 px-4 w-full my-1'>";
                                echo $error;
                                echo "</span>";
                            }
                            echo "</div>";
                        }
                    ?>
                <div class="w-full flex flex-wrap justify-start items-center text-xl mt-6 mb-4 border-b border-gray-400 pb-3">
                    <a href="#roles" class="text-gray-400 hover:text-gray-600 no-underline hover:underline transition-all duration-300 focus:underline focus:shadow-none focus:border-none">#</a>
                    <span class="text-gray-700 ml-2">Allowed Roles</span>
                </div>
                <div class="w-64 flex flex-wrap flex-col mt-3">
                    <?php
                        foreach($editable_roles as $key => $role){
                            $hide_this = $current_user_role == $key ? 'hidden' : 'grid';
                            $checked = isset($role['capabilities'][RUS_CAPABILITY]) ? 'checked' : '';

                            echo '<div class="'.$hide_this.' grid-cols-5 bg-teal-100 py-3 px-3 hover:bg-gray-200">';
                            echo '<div class="col-span-4">';
                            echo '<span class="capitalize mr-4 text-gray-700 text-base">'.$role['name'].'</span>';
                            echo '</div>';
                            echo '<div class="col-span-1 flex flex-wrap justify-end items-center">';
                            echo '<input type="checkbox" name="'.$key.'" value="'.$key.'" class="focus:outline-none focus:shadow-outline" '.$checked.'/>';
                            echo '</div>';
                            echo '</div>';
                        }
                    ?>
                </div>
                <div class="w-full flex flex-wrap justify-start items-center mt-6">
                <button type="submit" class="bg-teal-500 duration-300 transition-all flex felx-wrap justify-center items-center shadow-md py-2 px-4 focus:outline-none focus:shadow-outline select-none hover:bg-teal-400 text-white text-base font-medium rounded">
                    Save
                </button>
                </div>
            </form>
        </div>
        <style>
            .h-8{
                height: 2rem;
            }
            .w-8{
                width: 2rem;
            }
            .error, .settings-error, .notice{
                display:none !important;
            }
            #wpfooter{
                display: none !important;
            }
            body{
                background: #ffff !important;
            }

            .focus\:shadow-outline:focus {
                box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.5) !important;
            }

            html body #wpwrap #wpcontent #rusSettings{
                font-family: Inter, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji !important;
                line-height: 1.5 !important;
            }
        </style>
        <?php
    }
}
<?php
namespace Rus\Includes;

use Rus\Helper\RusHelper;
/**
 * Activation Class
 * 
 * @package    robust-user-search
 * @subpackage includes
 * @author     Smit Patel <smitpatel.dev@gmail.com>
 */
class RusActivation {

    /**
     * Security Check & register activation hook
     *
     * @param null
     * @return null
     */
    public function __construct($file){
        RusHelper::checkSecurity();
        register_activation_hook($file, [$this, 'activate']);
    }

    /**
     * Check roles and add robust_user_search if available
     *
     * @param null
     * @return null
     */
    public function activate(){
        $db_saved_roles = get_option('rus_allowed_roles',[]);

        // If plugin is installed first time
        if(empty($db_saved_roles)){
            self::resetRoles();
        } else {
            foreach($db_saved_roles as $role){
                $get_role = get_role($role);
                $get_role->add_cap(RUS_CAPABILITY, true);
            }
        }
    }

    /**
     * Reset roles & only allow administrator 
     *
     * @param null
     * @return null
     */
    private function resetRoles(){
        $roles = ['administrator'];
        $update_success = update_option('rus_allowed_roles',$roles);

        if($update_success){
            $get_roles = get_option('rus_allowed_roles');
            foreach($get_roles as $role){
                $get_role = get_role($role);
                $get_role->add_cap(RUS_CAPABILITY, true);
            }
        } else {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error is-dismissible">';
                echo '<p>Update option unsuccessful!</p>';
                echo '</div>';
            });
        }
    }
}
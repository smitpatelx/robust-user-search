<?php
namespace Rus\Includes;

use Rus\Helper\RusHelper;
/**
 * Deactivation Hook
 * 
 * @package    robust-user-search
 * @subpackage includes
 * @author     Smit Patel <smitpatel.dev@gmail.com>
 */
class RusDeactivation {

    /**
     * Security Check & register deactivation hook
     *
     * @param null
     * @return null
     */
    public function __construct($file){
        RusHelper::checkSecurity();
        register_deactivation_hook($file, [$this, 'deactivate']);
    }

    /**
     * Removing robust_user_search from every role in database 
     *
     * @param null
     * @return null
     */
    public function deactivate(){
        $db_saved_roles = get_option('rus_allowed_roles',[]);

        // If plugin is installed first time
        if(empty($db_saved_roles)){
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error is-dismissible">';
                echo '<p>Robust user search deactivation error. No roles found in the database</p>';
                echo '</div>';
            });
        } else {
            foreach($db_saved_roles as $role){
                $get_role = get_role($role);
                $get_role->remove_cap(RUS_CAPABILITY);
            } 
        }

        update_option('rus_allowed_roles',[]);
    }
}

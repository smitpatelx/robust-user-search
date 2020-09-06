<?php

function rus_deactivation(){
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
            $get_role->remove_cap('robust_user_search');
        } 
    }
}

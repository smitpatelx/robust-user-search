<?php

function rus_activation(){
    $db_saved_roles = get_option('rus_allowed_roles',[]);

    // If plugin is installed first time
    if(empty($db_saved_roles)){
        rus_reset_roles();
    } else {
        foreach($db_saved_roles as $role){
            $get_role = get_role($role);
            $get_role->add_cap('robust_user_search', true);
        }
    }
}

function rus_reset_roles(){
    $roles = ['administrator'];
    $update_success = update_option('rus_allowed_roles',$roles);

    if($update_success){
        $get_roles = get_option('rus_allowed_roles');
        foreach($get_roles as $role){
            $get_role = get_role($role);
            $get_role->add_cap('robust_user_search', true);
        }
    } else {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error is-dismissible">';
            echo '<p>Update option unsuccessful!</p>';
            echo '</div>';
        });
    }
}
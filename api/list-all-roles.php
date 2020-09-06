<?php

/*
 * Request url  : /wp-json/rus/v1/roles
 * Request type : GET
 * Params       : null
 * Return       : data[]
 */
function rus_get_all_roles(WP_REST_Request $request) {

    // rus_check_nonce from 'rus_includes/activation.php'
    $check_nonce = rus_check_nonce($request);
    if(!$check_nonce){
        return new WP_REST_Response(['status_code' => 400, 'message' => "You dont have permission to view all roles : ".wp_verify_nonce( $_REQUEST['X-WP-Nonce'], 'edit-users')], 400);
    }

    global $wp_roles;

    $all_roles = $wp_roles->roles;
    $editable_roles = apply_filters('editable_roles', $all_roles);

    return $editable_roles;
}
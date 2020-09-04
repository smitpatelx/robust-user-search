<?php

/*
 * Request url  : /wp-json/rus/v1/roles
 * Request type : GET
 * Params       : null
 * Return       : data[]
 */
function rus_get_all_roles() {
    global $wp_roles;

    $all_roles = $wp_roles->roles;
    $editable_roles = apply_filters('editable_roles', $all_roles);

    return $editable_roles;
}
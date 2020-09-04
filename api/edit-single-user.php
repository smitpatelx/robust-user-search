<?php

/*
 * Request url  : /wp-json/rus/v1/user/{id}
 * Request type : PUT
 * Params       : first_name, last_name, email, company, phone
 * Return       : data[]
 */

$errors = [];

function rus_edit_user(WP_REST_Request $request){
    extract($request->get_params());

    $data = [];
    
    // Validate Request
    if ( !is_valid_name($first_name) ){
        return new WP_REST_Response(['status_code' => 400, 'message' => "Invalid first name"], 400);
    } elseif ( !is_valid_name($last_name) ){
        return new WP_REST_Response(['status_code' => 400, 'message' => "Invalid last name"], 400);
    } elseif ( !filter_var($email, FILTER_VALIDATE_EMAIL) ){
        return new WP_REST_Response(['status_code' => 400, 'message' => "Invalid email address"], 400);
    } elseif ( !is_valid_phone($phone) ){
        return new WP_REST_Response(['status_code' => 400, 'message' => "Invalid phone number"], 400);
    }

    $data['ID']=$id;
    $data['first_name']=$first_name;
    $data['last_name']=$last_name;
    $data['user_email']=$email;


    $user_data = wp_update_user($data);
    
    $data = [];
    $data['billing_company']=$company;
    $data['billing_phone']=$phone;

    foreach($data as $key => $meta){
        $user_meta = update_user_meta($id, $key, $meta);
    }

    if ( is_wp_error( $user_data )) {
        // There was an error; possibly this user doesn't exist.
        return new WP_REST_Response(['status_code' => 400, 'message' => "User doesn't exist"], 400);
    } else {
        // User updated successfully
        return new WP_REST_Response(['status_code' => 200, 'message' => "User data updated successfully"], 200);
    }

}

function is_valid_email($val){
    if (filter_var($val, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }
}

function is_valid_name($val){
    if(preg_match ("/^[a-zA-Z\s]+$/",$val.trim()) && strlen($val.trim()) <= 25) {
        return true;
    } else {
        return false;
    }
}

function is_valid_phone($val){
    if(preg_match("/^[0-9]{10}$/", $val.trim()) && strlen($val.trim()) == 10){
        return true;
    } else {
        return false;
    }
}
<?php

/*
 * Request url  : /wp-json/rus/v1/user/{id}
 * Request type : GET
 * Params       : null
 * Return       : data[]
 */
function rus_get_single_user(WP_REST_Request $request){
    extract($request->get_params());
    $DBRecord = array();

    $user = get_user_by('ID', $id);
    $i=0;

    /*
     *  Helper Function : filter_null()
     *  Accept          : string,null,int
     *  Returns         : string
     */
    function filter_null($val){
        if($val===NULL) {
            return "";
        } else {
            return $val;
        }
    }

    // print_r[$user];
    $DBRecord['roles']                  = filter_null($user->roles);
    $DBRecord['username']               = filter_null($user->user_login);
    $DBRecord['id']                     = filter_null($user->ID);
    $DBRecord['first_name']             = filter_null($user->first_name);
    $DBRecord['last_name']              = filter_null($user->last_name);
    $DBRecord['user_registered']        = filter_null($user->user_registered);
    $DBRecord['email']                  = filter_null($user->user_email);

    $UserData = get_user_meta( $user->ID );  
    $DBRecord['billing_company']        = filter_null($UserData['billing_company'][0]);
    $DBRecord['billing_address_1']      = filter_null($UserData['billing_address_1'][0]);
    $DBRecord['billing_city']           = filter_null($UserData['billing_city'][0]);
    $DBRecord['billing_state']          = filter_null($UserData['billing_state'][0]);
    $DBRecord['billing_postcode']       = filter_null($UserData['billing_postcode'][0]);
    $DBRecord['billing_country']        = filter_null($UserData['billing_country'][0]);
    $DBRecord['billing_phone']          = filter_null($UserData['billing_phone'][0]);

    return new WP_REST_Response($DBRecord, 200);
}
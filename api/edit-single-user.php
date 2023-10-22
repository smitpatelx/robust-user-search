<?php
namespace Rus\Api;

use Rus\Helper\RusHelper;
use Rus\Helper\RusValidation;

/**
 * RestApi Class to edit user data
 * 
 * @package    robust-user-search
 * @subpackage api
 * @author     Smit Patel <smitpatel.dev@gmail.com>
 */
class RusRestApiPutEditUser {

    /**
     * Security Check & Registering rest route
     *
     * @param null
     * @return null
     */
    public function __construct(){
        RusHelper::checkSecurity();

        register_rest_route( 'rus/v1', '/user/(?P<id>\d+)', array(
            'methods' => 'PUT',
            'callback' => [$this,'processRequest'],
            'permission_callback' => function($request){	  
                return current_user_can(RUS_CAPABILITY);
            }
        ));
    }

    /**
     * Update user data
     *
     * @param string $first_name
     * @param string $last_name
     * @param string $email
     * @param string $company
     * @param string $phone
     * @param string $billing_country
     * @return json $data[]
     */
    public function processRequest(\WP_REST_Request $request){
        RusHelper::checkNonceApi($request);
        
        extract($request->get_params());

        $data = [];
        
        // Validate Request
        if ( !RusValidation::validateNames(sanitize_text_field($first_name)) ){
            return new \WP_REST_Response(['status_code' => 400, 'message' => "Invalid first name"], 400);
        } elseif ( !RusValidation::validateNames(sanitize_text_field($last_name))){
            return new \WP_REST_Response(['status_code' => 400, 'message' => "Invalid last name"], 400);
        } elseif ( !RusValidation::validateEmail(sanitize_email($email)) ){
            return new \WP_REST_Response(['status_code' => 400, 'message' => "Invalid email address"], 400);
        } elseif ( !RusValidation::validatePhone(sanitize_text_field($phone))){
            return new \WP_REST_Response(['status_code' => 400, 'message' => "Invalid phone number"], 400);
        }  elseif ( !RusValidation::validateCountryCode(sanitize_text_field($billing_country))){
            return new \WP_REST_Response(['status_code' => 400, 'message' => "Invalid country code"], 400);
        }

        $data['ID']                 = $id;
        $data['first_name']         = sanitize_text_field($first_name);
        $data['last_name']          = sanitize_text_field($last_name);
        $data['user_email']         = sanitize_email($email);

        $user_data                  = wp_update_user($data);
        
        $data                       = [];
        $data['billing_company']    = sanitize_text_field($company);
        $data['billing_country']    = sanitize_text_field($billing_country);
        $data['billing_phone']      = sanitize_text_field($phone);

        foreach($data as $key => $meta){
            update_user_meta($id, $key, $meta);
        }

        if ( is_wp_error( $user_data )) {
            // There was an error; possibly this user doesn't exist.
            return new \WP_REST_Response(['status_code' => 400, 'message' => "User doesn't exist"], 400);
        } else {
            // User updated successfully
            return new \WP_REST_Response(['status_code' => 200, 'message' => "User data updated successfully"], 200);
        }
    }
}
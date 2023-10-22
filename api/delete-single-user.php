<?php
namespace Rus\Api;

use Rus\Helper\RusHelper;
use Rus\Helper\RusValidation;

/**
 * RestApi Class to delete user data
 * 
 * @package    robust-user-search
 * @subpackage api
 * @author     Smit Patel <smitpatel.dev@gmail.com>
 */
class RusRestApiDeleteUser {

    /**
     * Security Check & Registering rest route
     *
     * @param null
     * @return null
     */
    public function __construct(){
        RusHelper::checkSecurity();

        register_rest_route( 'rus/v1', '/user/(?P<id>\d+)', array(
            'methods' => 'DELETE',
            'callback' => [$this, 'processRequest'],
            'permission_callback' => function($request){	  
                return current_user_can(RUS_CAPABILITY);
            }
        ));
    }

    /**
     * Delete user data
     *
     * @param string $id
     * @return json $data[]
     */
    public function processRequest(\WP_REST_Request $request){
        RusHelper::checkNonceApi($request);
        
        extract($request->get_params());

        $data = [];
        
        // Validate Request
        if ( !RusValidation::validateId(sanitize_text_field($id)) ){
            return new \WP_REST_Response(['status_code' => 400, 'message' => "Id is a required field."], 400);
        }

        $id = (int) $id;

        // Include user admin functions to get access to wp_delete_user().
        require_once(ABSPATH.'wp-admin/includes/user.php');

        $user_data = wp_delete_user($id);

        if (!$user_data) {
            // There was an error; possibly this user doesn't exist.
            return new \WP_REST_Response(['status_code' => 400, 'message' => "User doesn't exist."], 400);
        } else {
            // User updated successfully
            return new \WP_REST_Response(['status_code' => 200, 'message' => "User id: ".$id."deleted successfully."], 200);
        }
    }
}
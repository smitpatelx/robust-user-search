<?php
namespace Rus\Api;

use Rus\Helper\RusHelper;
/**
 * RestApi Class to get all roles
 * 
 * @package    robust-user-search
 * @subpackage api
 * @author     Smit Patel <smitpatel.dev@gmail.com>
 */
class RusRestApiGetRoles {

    /**
     * Security Check & Registering rest route
     *
     * @param null
     * @return null
     */
    public function __construct(){
        RusHelper::checkSecurity();
        
        register_rest_route( 'rsu/v1', '/roles', array(
            'methods' => 'GET',
            'callback' => [$this,'processRequest'],
            'permission_callback' => function($request){	  
                return current_user_can('robust_user_search');
            }
        ));
    }
    
    /**
     * Get all editable roles
     *
     * @param null 
     * @return json $data[]
     */
    public function processRequest(\WP_REST_Request $request) {

        RusHelper::checkSecurity();

        $check_nonce = RusHelper::checkNonce($request);
        if(!$check_nonce){
            return new \WP_REST_Response(['status_code' => 400, 'message' => "You dont have permission to view all roles"], 400);
        }

        global $wp_roles;

        $all_roles = $wp_roles->roles;
        $editable_roles = apply_filters('editable_roles', $all_roles);

        return new \WP_REST_Response($editable_roles, 200);
    }

    

}
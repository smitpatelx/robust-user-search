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
        
        register_rest_route( 'rus/v1', '/roles', array(
            'methods' => 'GET',
            'callback' => [$this,'processRequest'],
            'permission_callback' => function($request){	  
                return current_user_can(RUS_CAPABILITY);
            }
        ));
    }
    
    /**
     * Get all editable roles
     *
     * @param null 
     * @return json $data[]
     */
    function processRequest(\WP_REST_Request $request) {
        RusHelper::checkNonceApi($request);

        global $wp_roles;

        $all_roles = $wp_roles->roles;
        $editable_roles = apply_filters('editable_roles', $all_roles);

        return new \WP_REST_Response($editable_roles, 200);
    }
}
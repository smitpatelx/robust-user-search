<?php
namespace Rus\Api;

use Rus\Helper\RusHelper;
/**
 * RestApi Class to get single users
 * 
 * @package    robust-user-search
 * @subpackage api
 * @author     Smit Patel <smitpatel.dev@gmail.com>
 */
class RusRestApiGetUser {
    
    /**
     * Security Check & Registering rest route
     *
     * @param null
     * @return null
     */
    public function __construct(){
        RusHelper::checkSecurity();

        register_rest_route( 'rus/v1', '/user/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => [$this,'processRequest'],
            'permission_callback' => function($request){	  
                return current_user_can(RUS_CAPABILITY);
            }
        ));
    }

    /**
     * Get individual user data
     *
     * @param string $id
     * @return json $data[]
     */
    function processRequest(\WP_REST_Request $request){
        RusHelper::checkNonceApi($request);
        
        extract($request->get_params());
        $DBRecord = array();

        $user = get_user_by('ID', $id);
        $i=0;

        $DBRecord['roles']                  = RusHelper::filterNull($user->roles);
        $DBRecord['username']               = RusHelper::filterNull($user->user_login);
        $DBRecord['id']                     = RusHelper::filterNull($user->ID);
        $DBRecord['first_name']             = RusHelper::filterNull($user->first_name);
        $DBRecord['last_name']              = RusHelper::filterNull($user->last_name);
        $DBRecord['user_registered']        = RusHelper::filterNull($user->user_registered);
        $DBRecord['email']                  = RusHelper::filterNull($user->user_email);

        $UserData = get_user_meta( $user->ID );  
        $DBRecord['billing_company']        = RusHelper::filterNull($UserData['billing_company'][0]);
        $DBRecord['billing_address_1']      = RusHelper::filterNull($UserData['billing_address_1'][0]);
        $DBRecord['billing_city']           = RusHelper::filterNull($UserData['billing_city'][0]);
        $DBRecord['billing_state']          = RusHelper::filterNull($UserData['billing_state'][0]);
        $DBRecord['billing_postcode']       = RusHelper::filterNull($UserData['billing_postcode'][0]);
        $DBRecord['billing_country']        = RusHelper::filterNull($UserData['billing_country'][0]);
        $DBRecord['billing_phone']          = RusHelper::filterNull($UserData['billing_phone'][0]);

        return new \WP_REST_Response($DBRecord, 200);
    }
}
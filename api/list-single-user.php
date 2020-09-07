<?php
/**
 * RestApi Class to get single users
 * 
 * @package    robust-user-search
 * @subpackage api
 * @author     Smit Patel <smitpatel.dev@gmail.com>
 */
class RestApi_GetSingleUser {
    
    /**
     * Security Check & Registering rest route
     *
     * @param null
     * @return null
     */
    public function __construct(){
        RobustUserSearch::checkSecurity();

        register_rest_route( 'rsu/v1', '/user/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => [$this,'processRequest'],
            'permission_callback' => function($request){	  
                return current_user_can('robust_user_search');
            }
        ));
    }

    /**
     * Get individual user data
     *
     * @param string $id
     * @return json $data[]
     */
    function processRequest(WP_REST_Request $request){
        extract($request->get_params());
        $DBRecord = array();

        $user = get_user_by('ID', $id);
        $i=0;

        $DBRecord['roles']                  = self::filterNull($user->roles);
        $DBRecord['username']               = self::filterNull($user->user_login);
        $DBRecord['id']                     = self::filterNull($user->ID);
        $DBRecord['first_name']             = self::filterNull($user->first_name);
        $DBRecord['last_name']              = self::filterNull($user->last_name);
        $DBRecord['user_registered']        = self::filterNull($user->user_registered);
        $DBRecord['email']                  = self::filterNull($user->user_email);

        $UserData = get_user_meta( $user->ID );  
        $DBRecord['billing_company']        = self::filterNull($UserData['billing_company'][0]);
        $DBRecord['billing_address_1']      = self::filterNull($UserData['billing_address_1'][0]);
        $DBRecord['billing_city']           = self::filterNull($UserData['billing_city'][0]);
        $DBRecord['billing_state']          = self::filterNull($UserData['billing_state'][0]);
        $DBRecord['billing_postcode']       = self::filterNull($UserData['billing_postcode'][0]);
        $DBRecord['billing_country']        = self::filterNull($UserData['billing_country'][0]);
        $DBRecord['billing_phone']          = self::filterNull($UserData['billing_phone'][0]);

        return new WP_REST_Response($DBRecord, 200);
    }

    /**
     * Filter null values
     *
     * @param mixed $val
     * @return json $data[]
     */
    protected function filterNull($val){
        if($val===NULL) {
            return "";
        } else {
            return $val;
        }
    }
}
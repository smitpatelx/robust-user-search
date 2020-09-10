<?php
namespace Rus\Api;

use Rus\Helper\RusHelper;
/**
 * RestApi Class to get all users
 * 
 * @package    robust-user-search
 * @subpackage api
 * @author     Smit Patel <smitpatel.dev@gmail.com>
 */
class RusRestApiGetAllUsers {

    /**
     * Security Check & Registering rest route
     *
     * @param null
     * @return null
     */
    public function __construct(){
        RusHelper::checkSecurity();

        register_rest_route( 'rsu/v1', '/all', array(
            'methods' => 'GET',
            'callback' => [$this,'processRequest'],
            'permission_callback' => function($request){	  
                return current_user_can('robust_user_search');
            }
        ));
    }

    /**
     * List all users
     *
     * @param string $role
     * @return json $data[]
     */
    function processRequest(\WP_REST_Request $request){

        $check_nonce = RusHelper::checkNonce($request);
        if(!$check_nonce){
            return new \WP_REST_Response(['status_code' => 400, 'message' => "You dont have permission to view all roles"], 400);
        }
        
        extract($request->get_params());
        $DBRecord = array();
        $args = array(
            'orderby' => 'first_name',
            'order'   => 'ASC'
        );
        if(isset($role)){
            $args = array(
                'orderby' => 'first_name',
                'order'   => 'ASC',
                'role'    => $role
            );
        }
        $users = get_users( $args );
        $i=0;

        foreach ( $users as $user )
        {
            $DBRecord[$i]['roles']                  = self::filterNull($user->roles);
            $DBRecord[$i]['username']               = self::filterNull($user->user_login);
            $DBRecord[$i]['id']                     = self::filterNull($user->ID);
            $DBRecord[$i]['first_name']             = self::filterNull($user->first_name);
            $DBRecord[$i]['last_name']              = self::filterNull($user->last_name);
            $DBRecord[$i]['user_registered']        = self::filterNull($user->user_registered);
            $DBRecord[$i]['email']                  = self::filterNull($user->user_email);

            $UserData = get_user_meta( $user->ID );  
            $DBRecord[$i]['billing_company']        = self::filterNull($UserData['billing_company'][0]);
            $DBRecord[$i]['billing_address_1']      = self::filterNull($UserData['billing_address_1'][0]);
            $DBRecord[$i]['billing_city']           = self::filterNull($UserData['billing_city'][0]);
            $DBRecord[$i]['billing_state']          = self::filterNull($UserData['billing_state'][0]);
            $DBRecord[$i]['billing_postcode']       = self::filterNull($UserData['billing_postcode'][0]);
            $DBRecord[$i]['billing_country']        = self::filterNull($UserData['billing_country'][0]);
            $DBRecord[$i]['billing_phone']          = self::filterNull($UserData['billing_phone'][0]);
            $i++; 
        }
        return new \WP_REST_Response($DBRecord, 200);
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
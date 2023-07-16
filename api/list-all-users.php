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

        register_rest_route( 'rus/v1', '/all', array(
            'methods' => 'GET',
            'callback' => [$this,'processRequest'],
            'permission_callback' => function($request){	  
                return current_user_can(RUS_CAPABILITY);
            }
        ));
    }

    /**
     * List all users
     *
     * @param string $role
     * @param int $page
     * @param int $page_size
     * @param string|null $sort_by
     * @param string $search_text
     * @return json $data[]
     */
    function processRequest(\WP_REST_Request $request){
        RusHelper::checkNonceApi($request);
        
        extract($request->get_params());
        $DBRecord = [];

        // Pagination
        $page = isset($page) ? $page : 1;
        $page_size = isset($page_size) ? $page_size : 10;
        $offset = ($page - 1) * $page_size;

        // Sorting
        $sort_order = isset($sort_order) ? $sort_order : 'asc';
        $sort_by = self::filterIsSetNull($sort_by);

        // Search
        $search_text = self::filterNull($search_text);

        // Role
        $role = self::filterNull($role);

        global $wpdb;

        $sql = "
            SELECT * FROM {$wpdb->users}
            INNER JOIN {$wpdb->usermeta} ON ({$wpdb->users}.ID = {$wpdb->usermeta}.user_id)
            WHERE {$wpdb->users}.ID LIKE \"%$search_text%\" OR {$wpdb->users}.user_login LIKE \"%$search_text%\"
            OR {$wpdb->users}.user_email LIKE \"%$search_text%\" OR {$wpdb->users}.user_nicename LIKE \"%$search_text%\"
            OR {$wpdb->users}.display_name LIKE \"%$search_text%\" OR {$wpdb->usermeta}.meta_value LIKE \"%$search_text%\"
            GROUP BY {$wpdb->users}.ID
            ORDER BY {$wpdb->users}.ID $sort_order
            LIMIT $offset, $page_size
        ";

        $users = $wpdb->get_results($sql);

        $DBRecord['total'] = count($users);
        $DBRecord['page'] = $page;
        $DBRecord['page_size'] = $page_size;
        $DBRecord['users'] = array();
        $i=0;

        foreach ( $users as $user )
        {
            $record = array();
            $record['roles']                  = self::filterNull($user->roles);
            $record['username']               = self::filterNull($user->user_login);
            $record['id']                     = self::filterNull($user->ID);
            $record['user_registered']        = self::filterNull($user->user_registered);
            $record['email']                  = self::filterNull($user->user_email);

            $UserData = get_user_meta( $user->ID );  
            $record['roles']             = self::filterNull($UserData['wp_capabilities'][0]);
            
            // https://regex101.com/library/3q3RYF - smit
            // a:1:{s:11:"contributor";b:1;} ==to==> ["contributor"]
            $re = '/"([^"]+)"/';
            preg_match_all($re, $record['roles'], $matches, PREG_SET_ORDER, 0);
            if ($matches) {
                $record['roles'] = [];
                foreach ($matches as $key => $value) {
                    array_push($record['roles'], $value[1]);
                }
            }

            $record['first_name']             = self::filterNull($UserData['first_name'][0]);
            $record['last_name']              = self::filterNull($UserData['last_name'][0]);
            $record['billing_company']        = self::filterNull($UserData['billing_company'][0]);
            $record['billing_address_1']      = self::filterNull($UserData['billing_address_1'][0]);
            $record['billing_city']           = self::filterNull($UserData['billing_city'][0]);
            $record['billing_state']          = self::filterNull($UserData['billing_state'][0]);
            $record['billing_postcode']       = self::filterNull($UserData['billing_postcode'][0]);
            $record['billing_country']        = self::filterNull($UserData['billing_country'][0]);
            $record['billing_phone']          = self::filterNull($UserData['billing_phone'][0]);
            $DBRecord['users'][$i] = $record;
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
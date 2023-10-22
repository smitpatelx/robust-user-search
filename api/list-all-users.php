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
     * @param int $page
     * @param int $page_size
     * @param string|null $sort_by
     * @param string $search_text
     * @return json $data[]
     */
    function processRequest(\WP_REST_Request $request){
        RusHelper::checkNonceApi($request);
        
        extract($request->get_params());
        
        global $wpdb;
        $DBRecord = [];

        // Pagination
        $page = isset($page) ? $page : 1;
        $page_size = isset($page_size) ? $page_size : 10;
        $offset = ($page - 1) * $page_size;

        // Sorting
        $sort_order = isset($sort_order) ? strtoupper($sort_order) : 'ASC';
        $sort_by = self::mapTableColumns($sort_by, "t1", "t2");

        // Search
        $search_text = self::filterNull($search_text);
        $role = self::filterNull($role);

        $sql_on = "
            t1.ID = t2.user_id
            AND (t2.meta_value LIKE '%$role%' AND t2.meta_key = 'wp_capabilities')
        ";
        $sql_where = "
            t1.user_login LIKE '%$search_text%'
            OR t1.user_email LIKE '%$search_text%'
            OR t1.user_nicename LIKE '%$search_text%'
            OR t1.display_name LIKE '%$search_text%'
        ";

        $sql = "
            SELECT * FROM {$wpdb->users} as t1
            INNER JOIN {$wpdb->usermeta} as t2
            ON ($sql_on)
            WHERE
                $sql_where
            GROUP BY t2.user_id
            ORDER BY $sort_by $sort_order
            LIMIT $offset, $page_size
        ";

        $users = $wpdb->get_results($sql);

        // Get total records
        $sql_for_total_count = "
            SELECT COUNT(*) FROM {$wpdb->users} as t1
            INNER JOIN {$wpdb->usermeta} as t2
            ON ($sql_on)
            WHERE
                $sql_where
            GROUP BY t2.user_id
        ";
        $total_count_result = count($wpdb->get_results($sql_for_total_count));

        $DBRecord['total'] = (int) $total_count_result;
        $DBRecord['page'] = (int) $page;
        $DBRecord['page_size'] = (int) $page_size;
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
            
            // https://regex101.com/library/3q3RYF - smit
            // a:1:{s:11:"contributor";b:1;} ==to==> ["contributor"]
            $re = '/"([^"]+)"/';
            preg_match_all($re, $user->meta_value, $matches, PREG_SET_ORDER, 0);
            if ($matches) {
                $record['roles'] = [];
                foreach ($matches as $key => $value) {
                    array_push($record['roles'], $value[1]);
                }
            }

            $record['first_name']             = self::filterNullFirst($UserData['first_name']);
            $record['last_name']              = self::filterNullFirst($UserData['last_name']);
            $record['billing_company']        = self::filterNullFirst($UserData['billing_company']);
            $record['billing_address_1']      = self::filterNullFirst($UserData['billing_address_1']);
            $record['billing_city']           = self::filterNullFirst($UserData['billing_city']);
            $record['billing_state']          = self::filterNullFirst($UserData['billing_state']);
            $record['billing_postcode']       = self::filterNullFirst($UserData['billing_postcode']);
            $record['billing_country']        = self::filterNullFirst($UserData['billing_country']);
            $record['billing_phone']          = self::filterNullFirst($UserData['billing_phone']);
            $DBRecord['users'][$i] = $record;
            $i++; 
        }
        return new \WP_REST_Response($DBRecord, 200);
    }

    /**
     * Filter null values
     *
     * @param mixed $val
     * @return string ""
     */
    protected function filterNull($val){
        if($val===NULL) {
            return "";
        } else {
            return $val;
        }
    }

    /**
     * Filter null values
     *
     * @param mixed $val
     * @return string or NULL
     */
    protected function filterIsSetNull($val){
        if(isset($val)) {
            return $val;
        } else {
            return NULL;
        }
    }

    /**
     * Filter null values and return first value
     * 
     * @param mixed $val
     * @return string or ""
     */
    protected function filterNullFirst($val){
        if(!isset($val) || $val===NULL || !isset($val[0]) || $val[0]===NULL) {
            return "";
        } else {
            return $val[0];
        }
    }

    /**
     * Parse sort by text
     *
     * @param string $column_name
     * @param string $users_table
     * @param string $usermeta_table
     * @return string
     */
    protected function mapTableColumns($column_name, $users_table, $usermeta_table) {
        $sort = strtolower(self::filterNull($column_name));

        $sort_map = [
            'username' => "$users_table.user_login",
            'email' => "$users_table.user_email",
            'first_name' => "$users_table.user_nicename",
            'last_name' => "$users_table.user_login",
        ];

        if (isset($sort_map[$sort])) {
            return $sort_map[$sort];
        }
        return "$usermeta_table.meta_value";
    }
}
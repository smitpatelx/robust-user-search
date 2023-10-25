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

        if (trim($role) == '') {
            $sql_on = "
                t1.ID = t2.user_id
            ";
            $sql_on_2 = "
                t1.ID = t3.user_id
                AND t3.meta_value LIKE '%$search_text%'
            ";
        } else {
            $sql_on = "
                t1.ID = t2.user_id
                AND t2.meta_value LIKE '%$search_text%'
            ";
            $sql_on_2 = "
                t1.ID = t3.user_id
                AND (t3.meta_key = 'wp_capabilities'
                AND t3.meta_value LIKE '%$role%')
            ";
        }
        $sql_where = "
            t1.user_login LIKE '%$search_text%'
            OR t1.user_email LIKE '%$search_text%'
            OR t1.user_nicename LIKE '%$search_text%'
            OR t1.display_name LIKE '%$search_text%'
            OR t2.meta_value LIKE '%$search_text%'
        ";

        $sql = "
            SELECT * FROM {$wpdb->users} as t1
            INNER JOIN {$wpdb->usermeta} as t2
            ON ($sql_on)
            INNER JOIN {$wpdb->usermeta} as t3
            ON ($sql_on_2)
            WHERE
                $sql_where
            GROUP BY t2.user_id, t3.user_id, t1.user_login
            ORDER BY $sort_by $sort_order
            LIMIT $offset, $page_size
        ";

        $users = $wpdb->get_results($sql);

        // Get total records
        $sql_for_total_count = "
            SELECT COUNT(*) FROM {$wpdb->users} as t1
            INNER JOIN {$wpdb->usermeta} as t2
            ON ($sql_on)
            INNER JOIN {$wpdb->usermeta} as t3
            ON ($sql_on_2)
            WHERE
                $sql_where
            GROUP BY t2.user_id, t3.user_id, t1.user_login
        ";
        $total_count_result = count($wpdb->get_results($sql_for_total_count));

        $DBRecord['total'] = (int) $total_count_result;
        $DBRecord['page'] = (int) $page;
        $DBRecord['page_size'] = (int) $page_size;
        $DBRecord['users'] = array();

        // var_dump($users);
        foreach ($users as $user)
        {
            $record = array();
            $record['username'] = self::filterNull($user->user_login);
            $record['id'] = self::filterNull($user->ID);
            $record['user_registered'] = self::filterNull($user->user_registered);
            $record['email'] = self::filterNull($user->user_email);
            
            $user_data = get_user_meta($user->ID);
            
            $record['roles'] = self::extract_roles_from_meta_value($user_data['wp_capabilities']);
            $record['first_name'] = self::filterNullFirst($user_data['first_name']);
            $record['last_name'] = self::filterNullFirst($user_data['last_name']);
            $record['billing_company'] = self::filterNullFirst($user_data['billing_company']);
            $record['billing_address_1'] = self::filterNullFirst($user_data['billing_address_1']);
            $record['billing_city'] = self::filterNullFirst($user_data['billing_city']);
            $record['billing_state'] = self::filterNullFirst($user_data['billing_state']);
            $record['billing_postcode'] = self::filterNullFirst($user_data['billing_postcode']);
            $record['billing_country'] = self::filterNullFirst($user_data['billing_country']);
            $record['billing_phone'] = self::filterNullFirst($user_data['billing_phone']);
            array_push($DBRecord['users'], $record);
        }
        return new \WP_REST_Response($DBRecord, 200);
    }

    /**
     * Check if meta_value is array or not and extract roles from it
     *
     * @param string $meta_value
     * @return string[] $roles
     */
    protected function extract_roles_from_meta_value($meta_value) {
        $roles = [];

        if (!is_array($meta_value)) {
            return self::match_role($roles);
        } else {
            foreach ($meta_value as $value) {
                $role = self::match_role($value);
                if (!empty($role)) {
                    array_push($roles, $role);
                }
            }
        }

        return $roles;
    }

    /**
     * Extract role from meta_value
     *
     * @param string $meta_value
     * @return string ""
     */
    protected function match_role($meta_value) {
        // https://regex101.com/library/3q3RYF - smit
        // a:1:{s:11:"contributor";b:1;} ==to==> ["contributor"]
        $re = '/"([^"]+)"/';
        $matches = NULL;
        preg_match_all($re, $meta_value, $matches, PREG_SET_ORDER, 0);
        if (!empty($matches) && is_array($matches) && !is_null($matches)) {
            foreach ($matches as $value) {
                return $value[1];
            }
        }
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
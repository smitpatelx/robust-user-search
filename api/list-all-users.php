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
        $sort = isset($sort) ? strtoupper($sort) : 'ASC';
        $sort_by = self::mapTableColumns($sort_by, "t1");

        // Search
        $search_text = self::filterNull($search_text);
        $role = self::filterNull($role);

        $sql_select = "
            t1.ID,
            t1.user_login,
            t1.user_nicename,
            t1.user_email,
            t1.user_registered,
            t1.user_status,
            t1.display_name,
            t2.meta_value AS first_name,
            t3.meta_value AS last_name,
            t4.meta_value AS roles,
            t5.meta_value AS billing_company,
            t6.meta_value AS billing_country,
            t7.meta_value AS billing_phone
        ";

        $sql_left_join = "
            LEFT JOIN wp_usermeta AS t2 ON (
                t1.ID = t2.user_id
                AND t2.meta_key = 'first_name'
            )
            LEFT JOIN wp_usermeta AS t3 ON (
                t1.ID = t3.user_id
                AND t3.meta_key = 'last_name'
            )
            LEFT JOIN wp_usermeta AS t4 ON (
                t1.ID = t4.user_id
                AND t4.meta_key = 'wp_capabilities'
            )
            LEFT JOIN wp_usermeta AS t5 ON (
                t1.ID = t5.user_id
                AND t5.meta_key = 'billing_company'
            )
            LEFT JOIN wp_usermeta AS t6 ON (
                t1.ID = t6.user_id
                AND t6.meta_key = 'billing_country'
            )
            LEFT JOIN wp_usermeta AS t7 ON (
                t1.ID = t7.user_id
                AND t7.meta_key = 'billing_phone'
            )
        ";

        $sql_where = "
            (t1.user_login LIKE '%$search_text%'
            OR t1.user_email LIKE '%$search_text%'
            OR t1.user_nicename LIKE '%$search_text%'
            OR t1.display_name LIKE '%$search_text%'
            OR t2.meta_value LIKE '%$search_text%'
            OR t3.meta_value LIKE '%$search_text%'
            OR t5.meta_value LIKE '%$search_text%'
            OR t6.meta_value LIKE '%$search_text%'
            OR t7.meta_value LIKE '%$search_text%')
            AND t4.meta_value LIKE '%$role%'
        ";

        $sql = "
            SELECT 
                $sql_select
            FROM {$wpdb->users} as t1
            $sql_left_join
            WHERE
                $sql_where
            GROUP BY t1.ID
            ORDER BY $sort_by $sort
            LIMIT $offset, $page_size
        ";

        $users = $wpdb->get_results($sql);

        // Get total records
        $sql_for_total_count = "
            SELECT 
                COUNT(*)
            FROM {$wpdb->users} as t1
            $sql_left_join
            WHERE
                $sql_where
            GROUP BY t1.ID
        ";
        $total_count_result = count($wpdb->get_results($sql_for_total_count));

        $DBRecord['total'] = (int) $total_count_result;
        $DBRecord['page'] = (int) $page;
        $DBRecord['page_size'] = (int) $page_size;
        $DBRecord['users'] = array();

        // var_dump($users);
        foreach ($users as $user)
        {
            // var_dump($user);
            $record = array();

            // Populate from wp_users & wp_usermeta table
            $record['id'] = self::filterNull($user->ID);
            $record['username'] = self::filterNull($user->user_login);
            $record['user_registered'] = self::filterNull($user->user_registered);
            $record['email'] = self::filterNull($user->user_email);
            $record['roles'] = self::extract_roles_from_meta_value($user->roles);
            $record['first_name'] = self::filterNull($user->first_name);
            $record['last_name'] = self::filterNull($user->last_name);
            $record['billing_company'] = self::filterNull($user->billing_company);
            $record['billing_country'] = self::filterNull($user->billing_country);
            $record['billing_phone'] = self::filterNull($user->billing_phone);

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
            $role = self::match_role($meta_value);
            if (!empty($role)) {
                array_push($roles, $role);
            }
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
     * Parse sort by text
     *
     * @param string $column_name
     * @param string $users_table
     * @return string
     */
    protected function mapTableColumns($column_name, $users_table) {
        $sort = strtolower(self::filterNull($column_name));

        $sort_map = [
            'username' => "$users_table.user_login",
            'email' => "$users_table.user_email",
            'first_name' => "first_name",
            'last_name' => "last_name",
            'roles' => "roles",
            'company_name' => "billing_company",
            'phone' => "billing_phone",
            'created_at' => "$users_table.user_registered",
        ];

        if (isset($sort_map[$sort])) {
            return $sort_map[$sort];
        }
        return "$users_table.user_registered";
    }
}
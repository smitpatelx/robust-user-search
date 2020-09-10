<?php
namespace Rus\Helper;
/**
 * Robust User Search Main Class
 * 
 * @package    robust-user-search
 * @author     Smit Patel <smitpatel.dev@gmail.com>
 */

 class RusHelper {
     
    /**
     * Check if the constant ABSPATH is defined
     *
     * @param null
     * @return null
     */
    public static function checkSecurity(){
        defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' );
    }

    /**
     * Filter null values
     *
     * @param mixed $val
     * @return json $data[]
     */
    public function filterNull($val){
        if($val===NULL) {
            return "";
        } else {
            return $val;
        }
    }

    /** 
     * Check Nonce
     * 
     * @param WP_REST_Request $request
     * @return boolean
     */
    public function checkNonce($request){
        return wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest');
    }
 }
<?php
namespace Rus\Helper;
/**
 * Robust User Search Main Class
 * 
 * @package    robust-user-search
 * @author     Smit Patel <smitpatel.dev@gmail.com>
 */

 class RusValidation {
    /**
     * Validate id
     *
     * @param string $val
     * @return boolean
     */
    public static function validateId($val){
        if (is_numeric($val)) { return true; }
        return false;
    }

    /**
     * Validate phone number
     *
     * @param string $val
     * @return boolean
     */
    public static function validatePhone($val){
        if (empty(trim($val))) { return true; }
        if(preg_match("/^[0-9]{10}$/", trim($val)) && strlen(trim($val)) == 10){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Validate country code
     * 
     * @param string $val
     * @return boolean
     */
    public static function validateCountryCode($val){
        if (empty(trim($val))) { return true; }
        if(preg_match("/^[a-zA-Z]{2}$/", trim($val)) && strlen(trim($val)) == 2){
            return true;
        } else {
            return false;
        }
    }

     /**
     * Validate names
     *
     * @param string $val
     * @return boolean
     */
    public static function validateNames($val){
        if (empty(trim($val))) { return true; }
        if(preg_match ("/^[a-zA-Z\s]+$/",trim($val)) && strlen(trim($val)) <= 25) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Validate email
     * 
     * @param string $val
     * @return boolean
     */
    public static function validateEmail($val){
        if (empty(trim($val))) { return true; }
        if(filter_var($val, FILTER_VALIDATE_EMAIL)){
            return true;
        } else {
            return false;
        }
    }
 }
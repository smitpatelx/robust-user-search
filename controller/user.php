<?php
namespace Rus\Controller;

use Rus\Helper\RusHelper;
use Rus\Api\RusRestApiGetRoles;
use Rus\Api\RusRestApiGetAllUsers;
use Rus\Api\RusRestApiPutEditUser;
use Rus\Api\RusRestApiGetUser;
use Rus\Api\RusRestApiDeleteUser;

/**
 * RusUser controller to handle user api for this plugin
 * 
 * @package    robust-user-search
 * @subpackage Controller
 * @author     Smit Patel <smitpatel.dev@gmail.com>
 */
class RusUser {
    /**
     * Security Check & Registering rest route
     *
     * @param null
     * @return null
     */
    public function __construct(){
        RusHelper::checkSecurity();
    }

    public function init() {
        $instance = new self;
        $instance->includingFile();
        $instance->registerAllApi();
    }

    /**
     * Include all files
     * 
     * @param none
     * @return none
     */
    private function includingFile(){
        // Include all apis
        include_once(RUS_DIRECTORY.'/api/list-all-users.php');
        include_once(RUS_DIRECTORY.'/api/list-single-user.php');
        include_once(RUS_DIRECTORY.'/api/list-all-roles.php');
        include_once(RUS_DIRECTORY.'/api/edit-single-user.php');
        include_once(RUS_DIRECTORY.'/api/delete-single-user.php');
    }

    /**
     * Calls to register rest APIs
     * 
     * @param none
     * @return none
     */
    private function registerAllApi(){
        add_action( 'rest_api_init', function () {
            new RusRestApiGetRoles();
            new RusRestApiGetAllUsers();
            new RusRestApiPutEditUser();
            new RusRestApiGetUser();
            new RusRestApiDeleteUser();
        });
    }
}
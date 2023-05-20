<?php
namespace Rus;

use Rus\Helper\RusHelper;   
/**
 * Robust User Search Main Class
 * 
 * @package    robust-user-search
 * @author     Smit Patel <smitpatel.dev@gmail.com>
 */
class Constants {

    public function __construct(){
        RusHelper::checkSecurity();
        $this->define_rus_constants();
    }

    private function define_rus_constants(){
        global $wp_version;

        define('RUS_MINIMUM_WP_REQUIRED_VERSION', 5.2);
        define('RUS_DIRECTORY', __DIR__);
        define('RUS_FILE_PATH', __FILE__);
        define('RUS_WP_CURRENT_VERSION', $wp_version);
        define('RUS_CAPABILITY', 'robust_user_search');
        define('RUS_MENU_ICON_URL', "data:image/svg+xml;charset=UTF-8,%3Csvg width='110' height='110' viewBox='0 0 110 110' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M53.3907 4.87514C54.4283 4.48604 55.5717 4.48604 56.6093 4.87514L93.276 18.6251C95.0649 19.296 96.25 21.0061 96.25 22.9166V55C96.25 70.7961 85.792 83.418 76.3515 91.6785C71.5349 95.8929 66.74 99.1871 63.1583 101.426C61.3627 102.548 59.8598 103.412 58.7953 104.002C58.2628 104.297 57.839 104.523 57.5423 104.679C57.3939 104.757 57.2771 104.817 57.1943 104.859L57.0958 104.91L57.0661 104.925L57.0562 104.93C57.0547 104.93 57.0497 104.933 55 100.833C52.9503 104.933 52.9489 104.932 52.9474 104.931L52.9339 104.925L52.9042 104.91L52.8057 104.859C52.7229 104.817 52.6061 104.757 52.4577 104.679C52.161 104.523 51.7372 104.297 51.2047 104.002C50.1402 103.412 48.6373 102.548 46.8417 101.426C43.26 99.1871 38.4651 95.8929 33.6485 91.6785C24.208 83.418 13.75 70.7961 13.75 55V22.9166C13.75 21.0061 14.9351 19.296 16.724 18.6251L53.3907 4.87514ZM55 100.833L52.9474 104.931C54.2378 105.577 55.7594 105.578 57.0497 104.933L55 100.833ZM55 95.6209C55.8783 95.1231 57.0016 94.4639 58.3 93.6524C61.5933 91.5941 65.9651 88.5862 70.3152 84.7798C79.208 76.9986 87.0833 66.7038 87.0833 55V26.0929L55 14.0616L22.9167 26.0929V55C22.9167 66.7038 30.792 76.9986 39.6848 84.7798C44.0349 88.5862 48.4067 91.5941 51.7 93.6524C52.9984 94.4639 54.1217 95.1231 55 95.6209Z' fill='url(%23paint0_linear)'/%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M58.0295 31.3459C58.9118 31.7289 59.436 32.6477 59.3167 33.6021L57.4544 48.5H74.5001C75.3408 48.5 76.1056 48.9863 76.4622 49.7477C76.8188 50.509 76.7028 51.4079 76.1645 52.0538L54.4979 78.0537C53.8821 78.7927 52.8529 79.0372 51.9706 78.6542C51.0883 78.2712 50.5642 77.3524 50.6835 76.3979L52.5457 61.5H35.5001C34.6594 61.5 33.8945 61.0137 33.538 60.2524C33.1814 59.491 33.2974 58.5921 33.8356 57.9463L55.5023 31.9463C56.118 31.2074 57.1472 30.9629 58.0295 31.3459ZM40.126 57.1667H55.0001C55.6215 57.1667 56.2131 57.4335 56.6244 57.8994C57.0357 58.3653 57.2271 58.9854 57.15 59.6021L55.9 69.6024L69.8741 52.8334H55.0001C54.3786 52.8334 53.7871 52.5665 53.3758 52.1006C52.9645 51.6347 52.773 51.0146 52.8501 50.3979L54.1002 40.3977L40.126 57.1667Z' fill='url(%23paint1_linear)'/%3E%3Cdefs%3E%3ClinearGradient id='paint0_linear' x1='55' y1='4.58331' x2='55' y2='105.416' gradientUnits='userSpaceOnUse'%3E%3Cstop offset='0.408076' stop-color='%230A192F'/%3E%3Cstop offset='1' stop-color='%233EFFF3'/%3E%3C/linearGradient%3E%3ClinearGradient id='paint1_linear' x1='55.0001' y1='31.1666' x2='55.0001' y2='78.8335' gradientUnits='userSpaceOnUse'%3E%3Cstop offset='0.408076' stop-color='%230A192F'/%3E%3Cstop offset='1' stop-color='%233EFFF3'/%3E%3C/linearGradient%3E%3C/defs%3E%3C/svg%3E%0A");

        define('RUS_DIST_CSS_APP', plugins_url('/dist/css/app.css', __FILE__));
        define('RUS_DIST_JS_MANIFEST', plugins_url('/dist/js/manifest.js', __FILE__));
        define('RUS_DIST_JS_VENDOR', plugins_url('/dist/js/vendor.js', __FILE__));
        define('RUS_DIST_JS_APP', plugins_url('/dist/js/app.js', __FILE__));
        define('RUS_FONTS', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap');
    }

}

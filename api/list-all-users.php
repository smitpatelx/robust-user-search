<?php

/*
 * Request url  : /wp-json/rus/v1/all
 * Request type : GET
 * Params       : role[string]
 * Return       : data[]
 */
function rus_get_all_users(WP_REST_Request $request){
  extract($request->get_params());
  $DBRecord = array();
  $args = array(
      'orderby' => 'first_name',
      'order'   => 'ASC'
  );
  if($role){
      $args = array(
          'orderby' => 'first_name',
          'order'   => 'ASC',
          'role'    => $role
      );
  }
  $users = get_users( $args );
  $i=0;

  /*
   *  Helper Function : filter_null()
   *  Accept          : string,null,int
   *  Returns         : string
   */
  function filter_null($val){
      if($val===NULL) {
          return "";
      } else {
          return $val;
      }
  }

  foreach ( $users as $user )
  {
      // print_r[$user];
      $DBRecord[$i]['roles']                  = filter_null($user->roles);
      $DBRecord[$i]['username']               = filter_null($user->user_login);
      $DBRecord[$i]['id']                     = filter_null($user->ID);
      $DBRecord[$i]['first_name']             = filter_null($user->first_name);
      $DBRecord[$i]['last_name']              = filter_null($user->last_name);
      $DBRecord[$i]['user_registered']        = filter_null($user->user_registered);
      $DBRecord[$i]['email']                  = filter_null($user->user_email);

      $UserData = get_user_meta( $user->ID );  
      $DBRecord[$i]['billing_company']        = filter_null($UserData['billing_company'][0]);
      $DBRecord[$i]['billing_address_1']      = filter_null($UserData['billing_address_1'][0]);
      $DBRecord[$i]['billing_city']           = filter_null($UserData['billing_city'][0]);
      $DBRecord[$i]['billing_state']          = filter_null($UserData['billing_state'][0]);
      $DBRecord[$i]['billing_postcode']       = filter_null($UserData['billing_postcode'][0]);
      $DBRecord[$i]['billing_country']        = filter_null($UserData['billing_country'][0]);
      $DBRecord[$i]['billing_phone']          = filter_null($UserData['billing_phone'][0]);
      $i++; 
  }
  return new WP_REST_Response($DBRecord, 200);
}
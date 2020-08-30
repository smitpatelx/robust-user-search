<?php

function GetSubscriberUserData()
{
$DBRecord = array();
$args = array(
    'role'    => 'Subscriber',
    'orderby' => 'last_name',
    'order'   => 'ASC'
);
$users = get_users( $args );
$i=0;
foreach ( $users as $user )
  {
  $DBRecord[$i]['role']           = "Subscriber";   
  $DBRecord[$i]['WPId']           = $user->ID;  
  $DBRecord[$i]['FirstName']      = $user->first_name;
  $DBRecord[$i]['LastName']       = $user->last_name;
  $DBRecord[$i]['RegisteredDate'] = $user->user_registered;
  $DBRecord[$i]['Email']          = $user->user_email;

  $UserData                      = get_user_meta( $user->ID );  
  $DBRecord[$i]['Company']        = $UserData['billing_company'][0];    
  $DBRecord[$i]['Address']        = $UserData['billing_address_1'][0];
  $DBRecord[$i]['City']           = $UserData['billing_city'][0];
  $DBRecord[$i]['State']          = $UserData['billing_state'][0];
  $DBRecord[$i]['PostCode']       = $UserData['billing_postcode'][0];
  $DBRecord[$i]['Country']        = $UserData['billing_country'][0];
  $DBRecord[$i]['Phone']          = $UserData['billing_phone'][0];
  i++;
  }
return DBRecord;
}

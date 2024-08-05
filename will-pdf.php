<?php
/*
*
* Template Name: View Will
*
*
*/
if ( is_user_logged_in() ) {
    $user = get_userdata( get_current_user_id() );
    $user_roles = $user->roles;
    if(in_array( 'subscriber', $user_roles, true )){
        wp_die("You are not authorized to access.");
        exit;
    }
}
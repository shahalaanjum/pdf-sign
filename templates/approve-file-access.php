<?php
/* Template Name: approve file access  */

get_header();
$unique_code = isset($_GET['approve_code']) ? $_GET['approve_code'] : '';
$requester_email = isset($_GET['email_requester']) ? $_GET['email_requester'] : '';


echo 'Click the following link to approve access: <a href="" id="approve-access-file" data-uniqcode="'.$unique_code.'" data-requester_email="'.$requester_email.'" >click here</a>';

 
get_footer();
?>
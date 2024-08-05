<?php
// download.php
/* Template Name: will pdf video download  */

get_header();
global $wpdb;

// Retrieve the unique code and type from the query parameters
$unique_code = isset($_GET['code']) ? $_GET['code'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Validate the unique code and type
if (empty($unique_code) || !in_array($type, ['pdf', 'video'])) {
    die('Invalid request.');
}

// Query the database to verify the unique code
$table_name = 'will_uniquecode_approval_executor';
 $query = $wpdb->prepare("SELECT * FROM $table_name WHERE unique_code = %s", $unique_code);
$row = $wpdb->get_row($query);

if (!$row) {
    die('Invalid unique code.');
}

// Check if the code is approved or if 30 days have passed
$current_time = current_time('mysql');
$approval_status = $row->approved_sttaus;
$timestamp = $row->timestamp;
$access_time = strtotime($timestamp) + (30 * 24 * 60 * 60);

if ($approval_status != 1 ) {
    echo 'Access not allowed.<a href="" id="request-access-file" data-uniqcode="'.$unique_code.'" data-approver_email="'.$row->approver_email.'" >click here</a>';
}else{
    // Serve the appropriate file
    if ($type == 'pdf') {
        $file = $row->pdf_link;
    } else {
        $file = $row->video_link;
    }

    // Ensure the file exists
   
}
get_footer();
if (!($file)) {
    die('File not found.');
}

// Serve the file for download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file) . '"');
header('Content-Length: ' . filesize($file));
readfile($file);



?>

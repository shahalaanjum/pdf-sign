<?php
function create_request_form_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'will_request_form';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` varchar(20) NOT NULL,
            `name` TEXT NOT NULL,
            `email` varchar(255) NOT NULL,
            `otp` varchar(255) NOT NULL,
            `status` varchar(20) NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL
        ) $charset_collate;";
    
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
add_action('after_setup_theme', 'create_request_form_table');


add_action('wp_ajax_will_otp_update','will_otp_update_handeler' );
add_action('wp_ajax_nopriv_will_otp_update','will_otp_update_handeler'); 

function will_otp_update_handeler(){
    try{
        $id = $_POST['id'];
        $value = $_POST['value'];

        global $wpdb;
        $newdate = current_time('Y-m-d H:i:s');
        $table_name = $wpdb->prefix . 'will_request_form';

        $data_update = array(
            'status' => $value,
            'updated_at' => $newdate,
        );

        $where = array('id' => $id);
        $updated = $wpdb->update($table_name, $data_update, $where);

        echo  true;
    }catch(Exception $e){
        echo  false;
    }   
 wp_die();
} 




function will_request_list() {
     add_menu_page(
        'Will View List',
        'Will View List',
        'read',
        'will-wiew-list',
        'will_wiew_list_callback',
        'dashicons-editor-table',
        10
    );
}
add_action('admin_menu', 'will_request_list');

function will_wiew_list_callback(){
 
    ?> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
 
    <?php 
        global $wpdb;
        $table_name = $wpdb->prefix . 'will_request_form';

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['bulk_action']) && $_POST['bulk_action'] == 'trash') {
            if (isset($_POST['nonce_field']) && wp_verify_nonce($_POST['nonce_field'], 'bulk_action_nonce')) {
                if (isset($_POST['selected_rows']) && is_array($_POST['selected_rows'])) {
                    foreach ($_POST['selected_rows'] as $row_id) {
                        $row_id = intval($row_id);
                        $wpdb->delete($table_name, array('id' => $row_id), array('%d'));
                    }
                    wp_redirect($_SERVER['REQUEST_URI']);
                    exit;
                }
            }
        }
    ?> 
    <?php 
        $sql = $wpdb->prepare("SELECT * FROM $table_name");
        $result = $wpdb->get_results($sql);
    ?>
    <div class="container mt-4 unique-list">
    <div class="header-top row">
        <div class="col-lg-6"><h4 class="mb-5">Listing</h4></div>
    </div>
        <form method="post" action="#">
            <?php wp_nonce_field('bulk_action_nonce', 'nonce_field'); ?>
            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
                <select name="bulk_action" id="bulk-action-selector-top">
                    <option value="-1">Bulk actions</option>
                    <option value="trash">Delete</option>
                </select>
                <input type="submit" id="doaction" class="button action" value="Apply">
            </div>
            <table id="dataTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>
                            <!-- <input type="checkbox" id="select-all"> -->
                        </th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Code</th>
                        <th>Create Data</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($result)): ?>
                        <?php foreach ($result as $block): ?>
                            <tr>
                                <td><input type="checkbox" class="checkbox" name="selected_rows[]" value="<?= $block->id ?>"></td>
                                <td><?= $block->id ?></td>
                                <td><?= $block->name ?> </td>
                                <td><?= $block->email ?></td>
                                <td><?= $block->otp ?></td>
                                <td><?= $block->created_at ?></td>
                                <td>
                                    <?php if ($block->status == 0): ?>
                                        <button type="button" class="btn btn-warning action-btn" data-value="1"  data-id="<?php echo $block->id ?>">Deactive</button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-success action-btn" data-value="0" data-id="<?php echo $block->id ?>">Active</button>
                                    <?php endif ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>
    </div> 
    <script>
        jQuery(document).ready(function($) {
            jQuery('#dataTable').DataTable();


            $(document).on("click", ".action-btn", function(event) {
                event.preventDefault();
                var id = $(this).attr('data-id');
                var value = $(this).attr('data-value');
                $.ajax({
                    type: "post",
                    url: "<?php echo admin_url('admin-ajax.php');?>",
                    data: {
                        action: 'will_otp_update',
                        id: id,
                        value: value,
                    },
                    success: function(response) {
                        location.reload();
                    }
                });
            });

            $(document).on("click", ".export-file-ind", function(event) {
                event.preventDefault();
                var datatype = $(this).attr('data-id');
                $.ajax({
                    type: "post",
                    url: "<?php echo admin_url('admin-ajax.php');?>",
                    data: {
                        action: 'get_csv',
                        id: datatype,
                    },
                    success: function(response) {
                        var getData = JSON.parse(response);
                        if (getData.status) {
                            window.location.href = getData.download_link;
                            deleteFile(getData.deleteFile);
                        } else {
                            console.error(getData.message);
                        }
                    }
                });
            });
        });
    </script>
    <style type="text/css">
        button.export-file {
            float: right;
        }
        td.status span {
            padding: 5px;
            border-radius: 5px;
        }
        ul.pagination {
        justify-content: end;
        }
        div#dataTable_filter label {
            float: right;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .unique-list {
            font-size: 14px;
        }
        form.send_email {
            margin: 20px 0 0;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #cbcbcb;
            padding: 20px 0;
        }
        input#send_email {
            font-size: 17px;
        }
        form.send_email .form-group {
            margin: 0;
        }
        form.send_email .form-group {
            width: 80%;
        }
        a.row-title {
            color: #2271b1;
        }
    </style> 

    <?php 
}
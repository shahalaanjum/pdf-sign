
jQuery(document).ready(function($) {
$('#signForm').on('change', function(e) {
    e.preventDefault();

    var file_data = $('#signature-file').prop('files')[0];
    var form_data = new FormData();
    form_data.append('file', file_data);
    form_data.append('action', 'sign_frontend_ajax_upload');
    form_data.append('security', will_custom_ajax.nonce);

    $.ajax({
        url: will_custom_ajax.ajax_url,
        type: 'POST',
        data: form_data,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response.success) {
                alert('File uploaded successfully. File URL: ' + response.data.url);
            } else {
                alert('File upload failed: ' + response.data);
            }
        },
        error: function(response) {
            alert('An error occurred: ' + response.statusText);
        }
    });
});

});

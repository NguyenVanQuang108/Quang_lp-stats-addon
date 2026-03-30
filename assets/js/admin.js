/**
 * LearnPress Stats Dashboard - Admin JavaScript
 */
jQuery(document).ready(function($) {
    // Refresh stats via AJAX (optional)
    $('#lp-stats-refresh').on('click', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $widget = $button.closest('.postbox');
        
        $button.prop('disabled', true);
        $button.html('<span class="spinner is-active"></span> Đang cập nhật...');
        
        $.ajax({
            url: lp_stats_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'lp_stats_refresh',
                nonce: lp_stats_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $widget.find('.inside').html(response.data.html);
                } else {
                    alert('Có lỗi xảy ra: ' + response.data.message);
                }
            },
            complete: function() {
                $button.prop('disabled', false);
                $button.html('<i class="dashicons dashicons-update"></i> Làm mới');
            }
        });
    });
});
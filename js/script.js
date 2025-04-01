jQuery(function($) {
    
    function loadScholarships() {
        $.post(scholarship_ajax.ajax_url, {
            action: 'filter_scholarships',
            nonce: scholarship_ajax.nonce
        }, function(response) {
            $('#scholarship-list').html(response);
            $('.scholarship-accordion').off('click').on('click', function() {
                $(this).toggleClass('active')
                    .next('.scholarship-details').slideToggle();
            });
        }).fail(function(xhr, status, error) {
            console.error('Error loading scholarships:', error);
        });
    }

    // Initial load of scholarships
    loadScholarships();

    // Handle scholarship application form submission
    $(document).on('submit', '.scholarship-application', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(this);
        formData.append('action', 'submit_scholarship_application');

        // Get submit button and store original text
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.html();
        submitBtn.html('Submitting...').prop('disabled', true);

        $.ajax({
            url: scholarship_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    form.replaceWith('<div class="success-message">' + response.data.message + '</div>');
                } else {
                    alert('Error: ' + response.data.message);
                    submitBtn.html(originalText).prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred: ' + error);
            }
        });
    });
});
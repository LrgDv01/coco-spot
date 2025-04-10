
$(document).on('submit', '#editFieldForm', function(event) {
    event.preventDefault();  // Prevent form submission
    const fieldName = $('#fieldName').val();
    const fieldValue = $('#fieldValue').val();
    const user = window.userType === '1' ? 'farmer' : 'technician';
    $.ajax({
        url: `/user/${user}/update-field`,
        method: 'POST',
        data: {
            field: fieldName,
            value: fieldValue
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.success) {
                $('#' + fieldName).text(fieldValue);
                $('#editFieldModal').modal('hide');
                location.reload(); 
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
        }
    }); 
});


$(document).on('submit', '#updateProfileForm', function(event) {
    event.preventDefault();  // Prevent form submission
    const formData = {
        full_name: $('#new_name').val(),
        contact: $('#new_contact').val(),
        email: $('#new_email').val(),
        address: $('#new_address').val(),
    };
    const user = window.userType === '1' ? 'farmer' : 'technician';
    $.ajax({
        url: `/user/${user}/update-profile`,
        method: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            if (data.success) {
                $('#updateProfileModal').modal('hide');
                location.reload(); // Optionally reload the page to reflect changes
            } else {
                alert('Error updating profile: ' + data.message);
            }
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                // Show validation errors
                const errors = xhr.responseJSON.errors;
                let errorMessage = "Validation Errors:\n";
                for (let key in errors) {
                    errorMessage += `- ${errors[key]}\n`;
                }
                alert(errorMessage);
            } else {
                console.error('Error:', xhr);
                alert('An error occurred while updating the profile.');
            }
        }
    });
});

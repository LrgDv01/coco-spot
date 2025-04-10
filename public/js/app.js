
$(document).on('submit', '#registerform', function (e) { 
    e.preventDefault();
    let formData = new FormData(this);
    showLoading();
    $.ajax({
        method: "POST",
        url: "/registersubmit",
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) { 
            hideLoading();
            $('#registerform')[0].reset();
            global_showalert(response.message, 'Congrats', 'green', '/login');
        },
        error: function (xhr) {
            hideLoading();
            let response = xhr.responseJSON;
            let errorMessage = 'An error occurred';
            if (response && response.errors) {
                errorMessage = Object.values(response.errors).flat().join('\n');
                // Show inline alerts for specific fields
                if (response.errors.email) {
                    $('#email-alert').text(response.errors.email[0]).removeClass('d-none');
                }
            }
            global_showalert(errorMessage, 'Failed !', 'red');
        }
    });
});


$(document).on('submit', '#loginform', function (e) { 
    e.preventDefault();
    let formData = new FormData(this);
    showLoading();
    $.ajax({
        method: "POST",
        url: "/loginsubmit",
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {  
            hideLoading();
            global_showalert('', 'Login Success', 'green', response.redirect);
        },
        error: function (xhr) {
            hideLoading();
            $('#loginform')[0].reset();
            let response = JSON.parse(xhr.responseText);
            let errorMessage = 'An error occurred';
            if (response.errors) {
                errorMessage = '';
                for (let errorKey in response.errors) {
                    errorMessage += response.errors[errorKey][0] + '\n';
                }
            }
            global_showalert(response.errors, 'Failed !', 'red');
        }
    });
});

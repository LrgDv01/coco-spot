@include('partials.__header')
<main class="custombg min-vh-100">
    @include('partials.__nav')

    <div class="container py-3 d-flex justify-content-center align-items-center " style="height:90vh">
        <div class="card p-4" style="width: 400px; background: transparent; border:none;">
            <h4 class="text-center mb-4 text-white"><strong>Reset Password</strong></h4>
            <div id="response-message"></div>
            <form id="reset-password-form">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="mb-3">
                    <label for="email" class="form-label text-white"><strong>Email Address</strong></label>
                    <input type="email" id="email" name="email" value="{{ $email }}" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label text-white"><strong>New Password</strong></label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label text-white"><strong>Confirm Password</strong></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                </div>
                <div class="d-grid login-btn">
                    <button type="submit" class="btn btn-success">Login</button>
                </div>
            </form>
        </div>
    </div>
</main>
@include('partials.__footer')

<script>

    document.querySelector('form').addEventListener('submit', function(event) {
        const email = document.querySelector('input[name="email"]').value;
        if (!email) {
            alert('Email is required!');
            event.preventDefault();
        }
    });

    $('#reset-password-form').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();
            $.ajax({
                url: "/reset-password",
                method: "POST",
                data: formData, 
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function () {
                    showLoading();
                },
                success: function(response) {
                    if (response.success) {
                        $('.login-btn button').prop('disabled', true);
                        hideLoading();
                        $('#response-message')
                            .html(`<div style="color: white;">${response.message}</div>
                                <p style="color: white; margin-top: 10px;">
                                    Redirecting you to your dashboard... Please close this tab if not redirected.
                                </p>
                            `);

                        setTimeout(() => {
                            global_showalert(response.message, 'Login Success', 'green', response.redirect);
                        }, 1000); 

                    } else {
                        hideLoading();
                        $('#response-message')
                            .html(`<div style="color: white;">${response.message}</div>`);
                    }
                },
                error: function(xhr) {
                    hideLoading();
                    const errors = xhr.responseJSON.errors;
                    let errorMessages = '';
                    for (const key in errors) {
                        errorMessages += `<p>${errors[key][0]}</p>`;
                    }
                    $('#response-message').html(`<div style="color: red;">${errorMessages}</div>`);
                }
            });
        });

</script>

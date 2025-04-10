@include('partials.__header')
<main class="custombg min-vh-100 loginpagebg">
    <div class="container py-3">
        <div class="card bg-transparent border-0">
            <div class="card-body">
                <div class="text-center">
                    <h3 class="fw-bold text-white">Sign up</h3>
                    <p class="text-white">Already Have an Account ? <a href="{{ url('/') }}" style="text-decoration:underline;color:white;">
                        <strong>Login now</strong></a></p>
                </div>
                <form id="registerform" class="container py-3 was-validated">
                    @csrf
                    <div class="row mb-3 text-white">
                        <div class="col-lg-4">
                            <div class="form-group mb-1">
                                <label for="user_type" class="form-label fw-bold">USER TYPE</label>
                                <select name="user_type" class="form-select py-3" required>
                                    <option value="" disabled selected hidden>Select user type</option>
                                    <option value="1" {{ old('user_type') == 1 ? 'selected' : '' }}>Farmer</option>
                                    <option value="2" {{ old('user_type') == 2 ? 'selected' : '' }}>Technician</option>
                                </select>
                                @error('user_type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 mb-1">
                            <div class="form-group mb-1">
                                <label class="form-label fw-bold" for="user_name">USER NAME</label>
                                <input type="text" name="user_name" class="form-control py-3" id="user_name"
                                    placeholder="Enter your username" required autocomplete="username">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group mb-1">
                                <label class="form-label fw-bold" for="full_name">FULL NAME</label>
                                <input type="text" name="full_name" class="form-control py-3" id="full_name"
                                    placeholder="Enter your full name" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-5 text-white">
                        <div class="col-lg-4 mb-1">
                            <div class="form-group mb-1">
                                <label class="form-label fw-bold" for="address">ADDRESS</label>
                                <input type="text" name="address" class="form-control py-3" id="address"
                                    placeholder="Enter your address" required>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-1">
                            <label for="municipal" class="form-label fw-bold">MUNICIPAL</label>
                            <select class="form-select py-3" id="municipal" name="municipal" required>
                                <option value="" disabled selected hidden>Select Municipal</option>
                                <option value="Alaminos">Alaminos</option>
                                <option value="Calauan">Calauan</option>
                                <option value="Cavinti">Cavinti</option>
                                <option value="Famy">Famy</option>
                                <option value="Kalayaan">Kalayaan</option>
                                <option value="Liliw">Liliw</option>
                                <option value="Luisiana">Luisiana</option>
                                <option value="Lumban">Lumban</option>
                                <option value="Mabitac">Mabitac</option>
                                <option value="Magdalena">Magdalena</option>
                                <option value="Majayjay">Majayjay</option>
                                <option value="Nagcarlan">Nagcarlan</option>
                                <option value="Paete">Paete</option>
                                <option value="Pagsanjan">Pagsanjan</option>
                                <option value="Pakil">Pakil</option>
                                <option value="Pangil">Pangil</option>
                                <option value="Pila">Pila</option>
                                <option value="Rizal">Rizal</option>
                                <option value="San Pablo">San Pablo</option>
                                <option value="Santa Cruz">Santa Cruz</option>
                                <option value="Santa Maria">Santa Maria</option>
                                <option value="Siniloan">Siniloan</option>
                                <option value="Victoria">Victoria</option>
                            </select>
                        </div>
                        <div class="col-lg-4 mb-1">
                            <div class="form-group mb-1">
                                <label class="form-label fw-bold" for="contact">PHONE NUMBER</label>
                                {{--               <input type="text" name="contact" class="form-control py-3" id="contact"
                                    placeholder="Enter your contact number" pattern="^(09|\+639)\d{9}$" 
                                    title="Please enter a valid Philippine phone number (e.g., 09123456789 or +639123456789)" required> --}}
                  
                                    {{--       <div class="input-group">
                                    <span class="input-group-text">+63</span>
                                    <input type="text" name="contact" class="form-control py-3" id="contact"
                                        placeholder="Enter your contact number" pattern="^\d{9}$" 
                                        title="Please enter a valid Philippine phone number (e.g., 9123456789)" required>
                                </div>  --}}
                                
                                <div class="input-group">
    <span class="input-group-text">+63</span>
    <input type="tel" class="form-control py-3" id="contact-display"
           placeholder="e.g., 9123456789"
           pattern="^9\d{9}$"
           maxlength="10"
           title="Please enter a valid Philippine phone number (e.g., 9123456789)"
           required>
    <input type="hidden" name="contact" id="contact-full"> <!-- Will contain +639... -->
</div>
          
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group mb-1">
                                <label class="form-label fw-bold" for="email">EMAIL</label>
                                <input type="email" name="email" class="form-control py-3" id="email"
                                    placeholder="Enter your email" required>
                                <div id="email-alert" class="text-white d-none rounded p-1" 
                                    style="background-color:rgb(153, 119, 99); opacity: 0.7;">Email already taken!</div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group mb-1">
                                <label for="password" class="form-label fw-bold">PASSWORD</label>
                                <div class="position-relative">
                                    <input type="password" name="password" class="form-control py-3" id="new-password"
                                        placeholder="Create your password (min 8 characters)" required autocomplete="new-password" 
                                        title="Minimum 8 characters, at least one uppercase letter, one number, and one special character">
                                    <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y me-4 toggle-password" 
                                            data-target="#new-password">
                                        <i class="fa fa-eye-slash"></i>
                                    </button>
                                </div>

                                <div class="password-requirements mt-2 d-none">
                                    <div class="requirement" data-rule="length"><i class="fas fa-times text-danger"></i> Minimum 8 characters</div>
                                    <div class="requirement" data-rule="uppercase"><i class="fas fa-times text-danger"></i> At least one uppercase</div>
                                    <div class="requirement" data-rule="number"><i class="fas fa-times text-danger"></i> At least one number</div>
                                    <div class="requirement" data-rule="special"><i class="fas fa-times text-danger"></i> At least one special character</div>
                                </div>  

                                <div class="d-none password-strength">
                                    <div class="strength-bar"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group mb-1">
                                <label for="password_confirmation" class="form-label fw-bold">CONFIRM PASSWORD</label>
                                <div class="position-relative">
                                    <input type="password" name="password_confirmation" class="form-control py-3 pe-5" id="confirm-password"
                                           placeholder="Confirm your password" required autocomplete="new-password">
                                    <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y me-4 toggle-password" 
                                            data-target="#confirm-password">
                                        <i class="fa fa-eye-slash"></i>
                                    </button>
                                </div>
                                    
                                <div class="confirm-message mt-2 d-none rounded px-2" style="background-color:rgb(114, 148, 113); opacity: 0.7;">
                                    <i class="fas fa-check text-white"></i>
                                    <span class="text-white">Passwords match!</span>
                                </div>
                                <div class="error-message mt-2 d-none rounded px-2" style="background-color:rgb(153, 119, 99); opacity: 0.7;">
                                    <i class="fas fa-times text-white"></i>
                                    <span class="text-white">Passwords do not match!</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button class="btn btn-success px-5 py-2 fw-bold rounded rounded-3" type="submit">CREATE ACCOUNT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<style>
    .password-strength {
        width: 100%;
        height: 5px;
        background: #ddd;
        border-radius: 3px;
        margin-top: 5px;
    }

    .strength-bar {
        height: 100%;
        width: 0;
        border-radius: 3px;
        transition: all 0.3s ease;
    }

    .password-requirements .requirement {
        font-size: 0.8rem;
        margin: 2px 0;
    }

    .requirement .fa-check { display: none; }
    .requirement.valid .fa-check { display: inline-block; }
    .requirement.valid .fa-times { display: none; }

    .toggle-password {
        z-index: 5; 
        border: none !important;
        background: transparent !important;
        color: #6c757d !important; 
        padding: 0 !important;
    }

    .toggle-password:hover {
        color: #495057 !important; 
    }

    .form-control.pe-5 {
        padding-right: 3rem !important;
    }
</style>

@include('partials.__footer')
<script>


    document.querySelector('form').addEventListener('submit', function(e) {
        const displayInput = document.getElementById('contact-display');
        const fullInput = document.getElementById('contact-full');
        
        if (displayInput.value) {
            fullInput.value = '+63' + displayInput.value;
        }
    });



    $('#email').on('blur', function() {
        const email = $(this).val().trim();
        const emailAlert = $('#email-alert');
        const emailInput = $('#email');
        if (!email) {
            emailAlert.addClass('d-none');
            return;
        }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            emailAlert.text('Please enter a valid email address.').removeClass('d-none');
            return;
        }
        $.ajax({
            method: "GET",
            url: "/check-email",
            data: { email: email },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.available) {
                    emailAlert.addClass('d-none');
                } else {
                    emailAlert.text('Email already taken!').removeClass('d-none');
                    emailInput.addClass('border border-danger');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (xhr.status === 422 && response.errors?.email) {
                    emailAlert.text(response.errors.email[0]).removeClass('d-none');
                } else {
                    emailAlert.text('Error checking email availability.').removeClass('d-none');
                }
            }
        });
    });

    $('#email').on('input', function() {
        $('#email-alert').addClass('d-none');
    });

    // Password Strength Checker
    function checkPasswordStrength(password) {
        let strength = 0;
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            number: /\d/.test(password),
            special:  /[!@#$%^&*(),.?":{}|<>\/]/.test(password)
        };

        Object.entries(requirements).forEach(([rule, met]) => {
            const requirement = document.querySelector(`[data-rule="${rule}"]`);
            if (requirement) {
                requirement.classList.toggle('valid', met);
                requirement.classList.toggle('d-none', met); // Hide the requirement if met
            }
        });

        Object.values(requirements).forEach(met => met && strength++);
        return { strength: Math.min(4, strength), requirements };
    }


// Fixed password UI updater
function updatePasswordUI(password) {
    const result = checkPasswordStrength(password);
    const strengthBar = document.querySelector('.strength-bar');
    const passwordStrength = document.querySelector('.password-strength');
    const passwordRequirements = document.querySelector('.password-requirements');
    const colors = ['#ff0000', '#ff4d00', '#ffa500', '#99cc33', '#33cc33'];

    // Toggle visibility
    const hasContent = password.length > 0;
    passwordStrength.classList.toggle('d-none', !hasContent);
    passwordRequirements.classList.toggle('d-none', !hasContent);

    if (hasContent) {
        // Update strength bar
        strengthBar.style.width = `${(result.strength / 4) * 100}%`;
        strengthBar.style.backgroundColor = colors[result.strength];
    } else {
        // Reset requirements when empty
        document.querySelectorAll('.requirement').forEach(req => {
            req.classList.remove('valid');
        });
    }
}

  
    // Fixed password match checker with proper element scoping
function checkPasswordMatch() {
    const confirmInput = document.getElementById('confirm-password');
    const password = document.getElementById('new-password').value.trim();
    const confirmPassword = confirmInput.value.trim();
    
    // Get messages relative to confirm input container
    const container = confirmInput.closest('.form-group');
    const confirmMessage = container.querySelector('.confirm-message');
    const errorMessage = container.querySelector('.error-message');

    // Handle empty state
    if (!confirmPassword) {
        confirmMessage?.classList.add('d-none');
        errorMessage?.classList.add('d-none');
        return;
    }

    // Perform comparison with case sensitivity
    const matches = password === confirmPassword;
    
    // Toggle messages with animation
    if (matches) {
        confirmMessage?.classList.remove('d-none');
        errorMessage?.classList.add('d-none');
    } else {
        confirmMessage?.classList.add('d-none');
        errorMessage?.classList.remove('d-none');
    }
}

// Update event listeners for both fields
document.getElementById('new-password').addEventListener('input', function() {
    updatePasswordUI(this.value);
    checkPasswordMatch();
});

document.getElementById('confirm-password').addEventListener('input', checkPasswordMatch);


    // Form Submission Validation
    $(document).on('submit', '#registerform', function(e) { 
        const password = document.getElementById('new-password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        const result = checkPasswordStrength(password);

        if (result.strength < 3) { // Require at least "Medium" strength
            global_showalert('Password must be at least medium strength!', 'Weak Password', 'red');
            e.preventDefault();
            return;
        }

        if (password !== confirmPassword) {
            global_showalert('Passwords do not match!', 'Validation Error', 'red');
            e.preventDefault();
            return;
        }

    });

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const icon = this.querySelector('svg.svg-inline--fa');
                const target = document.querySelector(this.dataset.target);
                if (!target || !icon) {
                    console.error('Elements missing:', {target, icon});
                    return;
                }
                const isVisible = target.type === 'text';
                target.type = isVisible ? 'password' : 'text';
                icon.classList.toggle('fa-eye-slash', isVisible);
                icon.classList.toggle('fa-eye', !isVisible);
            });
        });
    });
</script>

@include('partials.__header')
<main class="custombg min-vh-100 loginpagebg">
@include('partials.__nav')
    <div class="container d-flex justify-content-center align-items-center py-3" style="height: 80vh">
        <div class="card logincardbg bg-transparent d-flex align-items-center b-none" style="width: 100vw; border:none">
            <div class="card-body">
                <div class="col d-flex flex-column align-items-center justify-content-center text-white">
                    <div class="col-lg-6 mb-5 rounded-circle bg-white p-3" style="width: 150px; height: 150px;">
                        <img src="{{ URL::asset('img/logo.png') }}" alt="Logo" class="img-fluid rounded-5">
                    </div>
                    <div class="w-500"  style="width: 90vw">
                        <div class="text-center">
                            <h3 class="fw-bold text-white">LOGIN</h3>
                            <p>Sign in to continue</p>
                        </div>
                        <form id="loginform" class="was-validated">
                            
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold" for="email">EMAIL</label>
                                <input type="email" name="email" class="form-control py-3" id="email" placeholder="Enter your email address" 
                                        required autocomplete="email">
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label fw-bold" for="password">PASSWORD</label>
                                <div class="position-relative">
                                    <input type="password" name="password" class="form-control py-3" id="password" placeholder="Enter your password" 
                                        required autocomplete="current-password">
                                    <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y me-5 toggle-password" data-target="#password">
                                        <i class="fa fa-eye-slash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group d-flex justify-content-between align-items-center mb-4">
                                <!-- Remember Me -->
                                <div class="form-check">
                                    <input type="hidden" name="remember" value="0">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"  value="1" 
                                        {{ session('remember_me') ? 'checked' : '' }}>
                                    <label class="form-check-label text-white" for="remember">Remember Me</label>
                                </div>
                                <!-- Forgot Password -->
                                <div>
                                    <a href="{{ route('request-form') }}" class="text-white text-decoration-none" 
                                        ><u>Forgot Password?</u></a>
                                </div>
                            </div>
                            @csrf
                            <div class="text-center">
                                <button class="btn btn-success px-5 py-2 fw-100 rounded rounded-3" type="submit">LOGIN</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>        
        </div>
    </div>

    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {!! isset($appInfo->terms_and_condition) ? $appInfo->terms_and_condition : 'Default terms and conditions content.' !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</main>
<style>
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

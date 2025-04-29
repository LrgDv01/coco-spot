@include('partials.__header')
{{-- <main class="custombg "> --}}

{{--  
 <main class="custombg loginpagebg" style="width: 100%; height: 100vh;">
    <!-- Hero Section -->
    <section id="hero" class="hero section d-flex flex-row-reverse justify-content-end align-items-center">
        <div class="container d-flex justify-content-end">
            <div class="bg-transparent bg-opacity-50">
                <!-- <div class="card-body"> -->
                    <div class="d-flex flex-column justify-content-center align-items-center mx-0 mt-5">
                        <div class="col-lg-6 mb-4 rounded-circle bg-white p-3" style="width: 30%; height: auto;">
                            <img src="{{ URL::asset('img/logo.png') }}" alt="Logo" class="img-fluid rounded-5">
                        </div>
                        <div class="col-lg-6 d-flex align-items-center text-white">
                            <div class="w-100">                          
                                <div class="text-center">
                                    <h3 class="fw-bold text-white">LOGIN</h3>
                                </div>
                                <form id="loginform" class="was-validated">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold" for="email">EMAIL</label>
                                        <input type="email" name="email" class="form-control py-3" id="email" 
                                                placeholder="Enter your email address" value="{{ old('email', session('email')) }}" required 
                                                autocomplete="email">
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
                                        <div class="form-check">
                                            <input type="hidden" name="remember" value="0">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember"  value="1" 
                                                {{ session('remember_me') ? 'checked' : '' }}>
                                            <label class="form-check-label text-white" for="remember">Remember Me</label>
                                        </div>
                                        @if ($device) 
                                            <div>
                                                <a href="{{ route('request-form') }}" class="text-white text-decoration-none" 
                                                    ><u>Forgot Password?</u></a>
                                            </div>
                                        @endif
                                    </div>
                                    @csrf
                                    <div class="form-group my-5">
                                        <div class="text-center mb-3"> 
                                            <button class="btn btn-success px-5 py-2 fw-bold rounded rounded-3" type="submit">LOGIN</button>
                                        </div>
                                        @if ($device) 
                                            <div class="text-center">
                                                <p class="text-white fs-6">Don't have an account yet? You can create one here.</p>
                                                <a href="{{ url('/register') }}" class="btn btn-success fw-bold px-5 rounded rounded-3 my-3">Sign Up Here</a>
                                            </div>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <!-- </div>         -->
            </div> 
        </div>
    </section><!-- /Hero Section -->
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

</style>  --}}

 <main class="custombg loginpagebg" style="width: 100%; min-height: 100vh;">
    <!-- Hero Section -->
    <section id="hero" class="hero section d-flex flex-column flex-md-row-reverse justify-content-center align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5 ms-auto">
                   {{--   <div class="d-flex flex-column align-items-center py-4 py-md-5 mt-5"> --}}
                    <div class="d-flex flex-column justify-content-center align-items-center mx-0 mt-5">
           
                        {{--              <!-- Logo -->
                        <div class="col-4 col-md-3 mb-4 mb-md-5">
                            <img src="{{ URL::asset('img/logo.png') }}" alt="Logo" 
                                 class="img-fluid rounded-circle bg-white p-2">
                        </div> --}}
                        <div class="col-lg-6 mb-4 rounded-circle bg-white p-3" style="width: {{ $device ? '40%' : '30%' }}; height: auto;">
                            <img src="{{ URL::asset('img/logo.png') }}" alt="Logo" class="img-fluid rounded-5">
                        </div>

                        <!-- Login Form -->
                        <div class="w-100">
                            <div class="text-center mb-2">
                                <h4 class="fw-bold text-white">LOGIN</h4>
                            </div>
                            
                            <form id="loginform" class="was-validated">
                                <!-- Email Input -->
                
                                {{--                 <div class="form-group mb-3">
                                    <label class="form-label fw-bold text-white" for="email">EMAIL</label>
                                    <input type="email" name="email" 
                                           class="form-control py-2 py-md-3" 
                                           placeholder="Email address" 
                                           required autocomplete="email">
                                </div> --}}

                                <div class="form-group mb-3">
                                        <label class="form-label fw-bold text-white" for="email">EMAIL</label>
                                        <input type="email" name="email" class="form-control py-2 py-md-3" id="email" 
                                                placeholder="Enter your email address" value="{{ old('email', session('email')) }}" required 
                                                autocomplete="email">
                                    </div>

                                <!-- Password Input -->
                                 {{--                                <div class="form-group mb-4">
                                    <label class="form-label fw-bold text-white" for="password">PASSWORD</label>
                                    <div class="input-group">
                                        <input type="password" name="password" 
                                               class="form-control py-2 py-md-3" 
                                               placeholder="Password" 
                                               required autocomplete="current-password">
                                        <button type="button" 
                                                class="btn btn-link text-white toggle-password">
                                            <i class="fa fa-eye-slash"></i>
                                        </button>
                                    </div>
                                </div>  --}}
 

                                <div class="form-group mb-4">
                                        <label class="form-label fw-bold text-white" for="password">PASSWORD</label>
                                        <div class="position-relative">
                                            <input type="password" name="password" class="form-control py-2 py-md-3" id="password" placeholder="Enter your password" 
                                            required autocomplete="current-password">
                                            <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y me-5 toggle-password" data-target="#password">
                                                <i class="fa fa-eye-slash"></i>
                                            </button>
                                        </div>
                                    </div>
                                {{-- 
                                                       <!-- Remember Me & Forgot Password -->
                                <div class="form-group d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
                                    <div class="form-check mb-2 mb-md-0">
                                        <input class="form-check-input" type="checkbox" id="remember">
                                        <label class="form-check-label text-white">Remember Me</label>
                                    </div>
                                    @if ($device)
                                    <div>
                                        <a href="{{ route('request-form') }}" 
                                           class="text-white text-decoration-none">
                                           <small><u>Forgot Password?</u></small>
                                        </a>
                                    </div>
                                    @endif
                                </div> --}}

                                <div class="form-group d-flex justify-content-between align-items-center mb-4">
                                        <div class="form-check">
                                            <input type="hidden" name="remember" value="0">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember"  value="1" 
                                                {{ session('remember_me') ? 'checked' : '' }}>
                                            <label class="form-check-label text-white" for="remember">Remember Me</label>
                                        </div>
                                        @if ($device) 
                                            <div>
                                                <a href="{{ route('request-form') }}" class="text-white text-decoration-none" 
                                                    ><u>Forgot Password?</u></a>
                                            </div>
                                        @endif
                                    </div>

                                @csrf
                                
                                <!-- Login Button -->
                                <div class="form-group my-4">
                                    {{--                    <div class="d-grid gap-4">
                                        <button class="btn btn-success py-2 py-md-2 fw-bold rounded-3" 
                                                type="submit">
                                            LOGIN
                                        </button>
                                    </div> --}}
                 
                                    <div class="text-center mb-3"> 
                                        <button class="btn btn-success px-5 py-2 py-md-2 fw-bold rounded rounded-3" type="submit">LOGIN</button>
                                    </div>

                                    @if ($device)
                                    <div class="text-center mt-4">
                                        <p class="text-white mb-3 small">Don't have an account?</p>
                                        <a href="{{ url('/register') }}" 
                                           class="btn btn-outline-light fw-bold px-4 rounded-3 small">
                                            Sign up
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main> 


<style>

.toggle-password {
    z-index: 5; /* Ensure it's above the input */
    border: none !important;
    background: transparent !important;
    color: #6c757d !important; /* Match Bootstrap's secondary color */
    padding: 0 !important;
}

.toggle-password:hover {
    color: #495057 !important; /* Darker color on hover */
}
.form-control.pe-5 {
    padding-right: 3rem !important;
}

/* Mobile-first media queries */
@media (max-width: 767.98px) {
    .hero {
        padding: 1rem;
    }
    
    .rounded-3 {
        border-radius: 0.5rem !important;
    }

    .form-control {
        font-size: 0.9rem;
    }
}

@media (max-width: 575.98px) {
    .bg-transparent {
        background: rgba(255, 255, 255, 0.1) !important;
    }

    .btn-success {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
}

@media (max-width: 400px) {
    .form-label {
        font-size: 0.85rem;
    }
    
    .text-white.mb-3 {
        font-size: 1.25rem;
    }
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


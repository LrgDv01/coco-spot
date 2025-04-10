@include('partials.__header')
{{-- <main class="custombg "> --}}
<main class="custombg min-vh-100 loginpagebg">
    <!-- Hero Section -->
    <section id="hero" class="hero section d-flex flex-row-reverse justify-content-end align-items-center">
        <div class="container d-flex justify-content-end">
            <div class="bg-transparent bg-opacity-50" style="width: 100vh">
                <!-- <div class="card-body"> -->
                    <div class="d-flex flex-column justify-content-center align-items-center mx-0 mt-5">
                        <div class="col-lg-6 mb-4 rounded-circle bg-white p-3" style="width: 150px; height: 150px;">
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
                                    <div class="form-group my-3">
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


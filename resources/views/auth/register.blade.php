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
                                <input type="text" name="contact" class="form-control py-3" id="contact"
                                    placeholder="Enter your contact number" required>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group mb-1">
                                <label class="form-label fw-bold" for="email">EMAIL</label>
                                <input type="email" name="email" class="form-control py-3" id="email"
                                    placeholder="Enter your email" required>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group mb-1">
                                <label for="password" class="form-label fw-bold">PASSWORD</label>
                                <input type="password" name="password" class="form-control py-3" id="new-password"
                                    placeholder="Create your password" required autocomplete="new-password">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group mb-1">
                                <label for="password_confirmation" class="form-label fw-bold">CONFIRM PASSWORD</label>
                                <input type="password" name="confirm-password" class="form-control py-3" id="password"
                                    placeholder="Confirm your password" required autocomplete="new-password">
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
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
@include('partials.__footer')

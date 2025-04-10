<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
  private function validateCsrfToken(Request $request)
  {
    if (!$request->has('_token') || $request->session()->token() !== $request->input('_token')) {
      return response()->json(['message' => 'Invalid CSRF token'], 419);
    }
    return null;
  }
  public function login(Request $request)
  {
    $csrfValidation = $this->validateCsrfToken($request);
    if ($csrfValidation) {
      return $csrfValidation;
    }

    $validator = Validator::make($request->all(), [
      'email' => 'required|email',
      'password' => 'required|string',
      'remember' => 'nullable|boolean', 
    ]);

    if ($validator->fails()) {
      $email = $request->input('email');
      return response()->json(['errors' => 'Validation failed', 'errors' => $validator->errors()], 422);
    }
    // Attempt to log in the user
    $credentials = $request->only('email', 'password');
    $remember = $request->boolean('remember'); 

    $checkData = User::where('email', $request->email)->first();
    if($checkData) {
        if (Auth::attempt($credentials, $remember)) {
          // Store "Remember Me" preference in session
          $request->session()->put('remember_me', $remember); // Save in session
          // Authentication successful
          $user = Auth::user();
          $redirectRoute = $user->isAdmin()
            ? route('admin.dashboard'): ($user->isFarmer() 
            ? route('user.farm')
            : route('user.notifications'));
          return response()->json(['message' => 'Login successful', 'user' => $user, 'redirect' => $redirectRoute], 200);
        } else {
          // Authentication failed
          $request->session()->flash('email', $request->input('email'));
          return response()->json(['errors' => 'Invalid credentials'], 401);
        }
    } else {
      return response()->json(['errors' => 'Invalid credentials'], 401);
    }
    
  }

  public function checkEmail(Request $request)
  {
      $validated = $request->validate([
          'email' => 'required|email'
      ]);

      $exists = User::where('email', $validated['email'])->exists();
      return response()->json(['available' => !$exists]);
  }

  public function register(Request $request)
  {
    $csrfValidation = $this->validateCsrfToken($request);
    if ($csrfValidation) {
      return $csrfValidation;
    }
 
    try {

      $validated = $request->validate([
        'user_name' => 'required|string|max:255',
        'full_name' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'municipal' => 'required|string|max:255',
        'contact' => [
            'required',
            'string',
            'regex:/^(09|\+639)\d{9}$/',
            'unique:users',
        ],
        'email' => 'required|string|email|max:255|unique:users',
    'password' => [
      'required',
      'string',
      'min:8',
      'confirmed',
      'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[!@#$%^&*(),.?":{}|<>\/])[A-Za-z\\d!@#$%^&*(),.?":{}|<>\/]{8,}$/'
  ],
        'user_type' => 'required|integer|in:1,2',
    ], [
        'password.regex' => 'The password must contain at least: 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character (!@#$%^&*(),.?":{}|<>/).',
        'contact.regex' => 'Please enter a valid Philippine mobile number (09 or +639 format).'
    ]);

        // Create a new user
        $user = User::create([
          'user_name' => $validated['user_name'],
          'full_name' => $validated['full_name'],
          'address' => $validated['address'],
          'municipal' => $validated['municipal'],
          'contact' => $validated['contact'],
          'email' =>$validated['email'],
          'password' => Hash::make($validated['password']),
          'user_type' => $validated['user_type'],
        ]);
        
        return response()->json(['success' => true, 'message' => 'User registered successfully'], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
  }

  // protected function messages()
  // {
  //     return [
  //         'password.required' => 'Password is required.',
  //     ];
  // }

  public function showRequestForm() {
    return view('auth.request_link_verification');
  }

  public function requestResetLink(Request $request)
  {
    $request->validate([
      'email' => 'required|email|exists:users,email',
    ]);

    $status = Password::sendResetLink(
      $request->only('email')
    );

    if ($status == Password::RESET_LINK_SENT) {
      return response()->json(['success' => true, 'message' => 'We have e-mailed your password reset link!']);
    } else {
      return response()->json(['success' => false, 'message' => 'Failed to send reset link, please try again.'], 422);
    }
  }

  public function showResetForm($token) {
    $email = request('email');
    return view('auth.reset_password', ['token' => $token, 'email' => $email]);
  }

  public function resetPassword(Request $request) {

    $request->validate([
      'token' => 'required',
      'email' => 'required|email|exists:users,email',
      'password' => 'required|confirmed|min:3',
    ]);

    $status = Password::reset(
      $request->only('email', 'password', 'password_confirmation', 'token'),
      function ($user, $password) {
        $user->forceFill([
          'password' => Hash::make($password),
        ])->save();
        // Automatically log the user in
        Auth::login($user);
      }
    );

    if ($status === Password::PASSWORD_RESET) {
      $user = Auth::user();
      $redirectRoute = $user->isAdmin() 
        ? route('admin.dashboard'): ($user->isFarmer() 
        ? route('user.farm')
        : route('user.notifications'));
      return response()->json([
        'success' => true, 
        'message' => 'Password has been reset and you are now logged in.', 
        'user' => $user, 'redirect' => $redirectRoute], 200);
    } 
        // If reset fails, redirect back with an error
        return back()->withErrors(['success' => false, 'email' => __($status)]);
    // return response()->json([
    //   'success' => false,
    //   'message' => __($status),
    // ], 400);
  }

  public function logout(Request $request)
  {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    // return redirect()->route('home');
    return redirect('/');
  }
}

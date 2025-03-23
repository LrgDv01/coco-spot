<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class ProfileController extends Controller
{
    public function index() {

        $user = auth()->user(); // Get the authenticated user
        // Return view with user data
        return view('user.pages.settings', ['user' => $user]);
    }

    public function updateProfileImage(Request $request) {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
        ]);

        $user = Auth::user();

        // Delete the old profile image if it exists
        if ($user->profile_img) {
            Storage::disk('public')->delete($user->profile_img);
        }

        // Store the new profile image
        $imagePath = $request->file('profile_image')->store('images', 'public'); 

        // Update user details
        $user->profile_img = $imagePath;
        $user->save();
        
        return redirect()->back()->with('success', 'Profile image updated successfully.');
    }

    public function updateField(Request $request) {
        $user = auth()->user();
        $field = $request->input('field');
        $value = $request->input('value');
    
        // if (in_array($field, ['user_name', 'age', 'gender', 'contact_number', 'email', 'municipality', 'district'])) {
        if (in_array($field, ['user_name', 'contact', 'email'])) {

            $user->$field = $value;
            $user->save();
    
            return response()->json(['success' => true]);
        }
    
        return response()->json(['success' => false], 400);
    }


    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'contact' => 'required|string|max:15',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }
    
        $user = Auth::user();
        // Update user details
        $user->full_name = $request->input('full_name');
        $user->contact = $request->input('contact');
        $user->email = $request->input('email');
        // Save changes and respond
        if ($user->save()) {
            return response()->json(['success' => true, 'message' => 'Profile updated successfully.'], 200);
        }
    
        return response()->json([
            'success' => false,
            'message' => 'Failed to update profile. Please try again later.'
        ], 500);
    }
    
    
}

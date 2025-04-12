<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    public function index() {
        $user = auth()->user(); // Get the authenticated user
        // Return view with user data
        return view('user.settings', ['user' => $user]);
    }

    // public function updateProfileImage(Request $request) {
    //     $request->validate([
    //         'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
    //     ]);
    //     $user = Auth::user();
    //     // Delete the old profile image if it exists
    //     if ($user->profile_img) {
    //         Storage::disk('public')->delete($user->profile_img);
    //     }
    //     // Store the new profile image
    //     $imagePath = $request->file('profile_image')->store('images', 'public'); 
    //     // Update user details
    //     $user->profile_img = $imagePath;
    //     $user->save();
    //     return redirect()->back()->with('success', 'Profile image updated successfully.');
    // }

    public function updateProfileImage(Request $request) {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|min:0', 
        ]);
        $user = Auth::user();
        // Delete the old profile image from both locations if it exists
        if ($user->profile_img) {
            // Delete from storage/app/public
            Storage::disk('public')->delete($user->profile_img);
            // Delete from public/storage
            $oldFilename = basename($user->profile_img);
            $oldPublicPath = public_path('storage/images/' . $oldFilename);
            if (File::exists($oldPublicPath)) {
                File::delete($oldPublicPath);
            }
        }
        $imagePath = $request->file('profile_image')->store('images', 'public'); 
        $filename = basename($imagePath);
        $storagePath = storage_path('app/public/images/' . $filename);
        $publicPath = public_path('storage/images/' . $filename);
        // Ensure the directory exists
        if (!File::exists(dirname($publicPath))) {
            File::makeDirectory(dirname($publicPath), 0755, true);
        }
        File::copy($storagePath, $publicPath);
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
        if (in_array($field, ['user_name', 'contact', 'email', 'address'])) {
            $user->$field = $value;
            $user->save();
            return response()->json(['success' => true], 200);
        }
        return response()->json(['success' => false], 400);
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'contact' => 'required|string|max:15',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'address' => 'required|string|max:255',
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
        $user->address = $request->input('address');
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

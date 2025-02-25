<?php

namespace App\Http\Controllers\user;
use App\Models\Farm\FarmModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FarmController extends Controller
{

    public function index() 
    {
        $userId = Auth::id(); 
        $farms = FarmModel::where('farmer_id', $userId)
        // ->orderBy('created_at', 'desc')->limit(4) // Order by created_at, newest first
        ->orderBy('created_at', 'desc') // Order by created_at, newest first
        ->get();
        return view('user.home', compact('farms'));
    }

    public function updateCondition(Request $request) 
    {
        $request->validate([
            'condition' => 'required|string|max:255',
            'farm_id' => 'required|exists:farms,id',
        ]);
        $farm = FarmModel::findOrFail($request->farm_id);
        $farm->update([
            'condition' => $request->condition,
        ]);
        return redirect()->back()->with('success', 'Condition updated successfully!');
    }

    // public function store(Request $request)
    // {
    //     // Validate the incoming request
    //     $request->merge(['farmer_id' => auth()->id()]);
    //     try {
    //         $validated = $request->validate([
    //             'farmer_id' => 'required|exists:users,id',
    //             'name' => 'required|string|max:255',
    //             'location' => 'required|string|max:255',
    //             'district' => 'required|string|max:255',
    //             'variety' => 'nullable|string|max:255',
    //             'hectares' => 'required|numeric|min:0',
    //             'tree_age' => 'nullable|integer|min:0',
    //             'planted_coconut' => 'nullable|integer|min:0',
    //             'condition' => 'nullable|string|max:255',
    //         ]);
    //         if (!isset($validated['condition'])) {
    //             $validated['condition'] = 'is Healthy'; 
    //         }
    //         $farm = FarmModel::create($validated);
    //         return response()->json(['success' => true, 'farm' => $farm]);
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         return response()->json([
    //             'errors' => $e->errors()
    //         ], 422);
    //     }
    // }

    public function store(Request $request)
    {
        $request->merge(['farmer_id' => auth()->id()]);
        try {
            $validated = $request->validate([
                'farmer_id' => 'required|exists:users,id',
                'name' => 'required|string|max:255',
                'location' => 'required|string|regex:/^-?\d+(\.\d+)?,\s*-?\d+(\.\d+)?$/',
                'address' => 'nullable|string|max:255',
                'district' => 'required|string|max:255',
                'variety' => 'nullable|string|max:255',
                'hectares' => 'required|numeric|min:0',
                'tree_age' => 'nullable|integer|min:0',
                'planted_coconut' => 'nullable|integer|min:0',
            ]);
            [$latitude, $longitude] = array_map('floatval', explode(',', $validated['location']));
            unset($validated['location']);
            $validated['latitude'] = $latitude;
            $validated['longitude'] = $longitude;
            $validated['address'] = $request->input('address');
            $farm = FarmModel::create($validated);
            return response()->json(['success' => true, 'farm' => $farm]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }
}

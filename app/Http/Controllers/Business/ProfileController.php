<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display a listing of profiles
     */
    public function index(Request $request)
    {
        $query = Profile::with('qrCodes')->latest();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('uuid', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $profiles = $query->paginate(20);

        return view('profiles.index', compact('profiles'));
    }

    /**
     * Show the form for creating a new profile (Admin side)
     */
    public function create()
    {
        $availableQrCodes = QrCode::active()
            ->withoutProfile()
            ->whereDoesntHave('orderItems')
            ->get();

        return view('profiles.create', compact('availableQrCodes'));
    }

    /**
     * Store a newly created profile (Admin side)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1|max:150',
            'date_of_birth' => 'nullable|date|before:today',
            'email' => 'required|email|unique:profiles,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'height' => 'nullable|numeric|min:0|max:300',
            'weight' => 'nullable|numeric|min:0|max:500',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'nullable|string',
            'emergency_contacts' => 'nullable|array|max:4',
            'emergency_contacts.*.name' => 'required|string|max:255',
            'emergency_contacts.*.phone' => 'required|string|max:20',
            'chronic_conditions' => 'nullable|array|max:5',
            'chronic_conditions.*.name' => 'required|string|max:255',
            'chronic_conditions.*.status' => 'nullable|string|max:255',
            'current_medications' => 'nullable|array|max:10',
            'current_medications.*.name' => 'required|string|max:255',
            'current_medications.*.dosage' => 'nullable|string|max:255',
            'current_medications.*.frequency' => 'nullable|string|max:255',
            'medical_history' => 'nullable|string',
            'medical_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'important_note' => 'nullable|string',
            'profile_image' => 'nullable|image|max:2048',
            'qr_code_id' => 'nullable|exists:qr_codes,id',
        ]);

        try {
            // Handle profile image
            if ($request->hasFile('profile_image')) {
                $validated['profile_image'] = $request->file('profile_image')->store('profiles/images', 'public');
            }

            // Handle medical files
            if ($request->hasFile('medical_files')) {
                $medicalFiles = [];
                foreach ($request->file('medical_files') as $file) {
                    $medicalFiles[] = $file->store('profiles/medical-files', 'public');
                }
                $validated['medical_files'] = $medicalFiles;
            }

            $profile = Profile::create($validated);

            // Link QR code if provided
            if ($request->qr_code_id) {
                $profile->linkQrCode($request->qr_code_id, Auth::id(), 'Profile created by admin');
            }

            return redirect()
                ->route('profiles.show', $profile)
                ->with('success', 'Profile created successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create profile: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified profile
     */
    public function show(Profile $profile)
    {
        $profile->load(['qrCodes']);
        
        return view('profiles.show', compact('profile'));
    }

    /**
     * Show the form for editing the specified profile
     */
    public function edit(Profile $profile)
    {
        return view('profiles.edit', compact('profile'));
    }

    /**
     * Update the specified profile
     */
    public function update(Request $request, Profile $profile)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1|max:150',
            'date_of_birth' => 'nullable|date|before:today',
            'email' => 'required|email|unique:profiles,email,' . $profile->id,
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'height' => 'nullable|numeric|min:0|max:300',
            'weight' => 'nullable|numeric|min:0|max:500',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'nullable|string',
            'emergency_contacts' => 'nullable|array|max:4',
            'chronic_conditions' => 'nullable|array|max:5',
            'current_medications' => 'nullable|array|max:10',
            'medical_history' => 'nullable|string',
            'medical_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'important_note' => 'nullable|string',
            'profile_image' => 'nullable|image|max:2048',
            'status' => 'nullable|in:active,inactive,suspended',
        ]);

        try {
            // Remove password if not provided
            if (empty($validated['password'])) {
                unset($validated['password']);
            }

            // Handle profile image
            if ($request->hasFile('profile_image')) {
                if ($profile->profile_image) {
                    Storage::disk('public')->delete($profile->profile_image);
                }
                $validated['profile_image'] = $request->file('profile_image')->store('profiles/images', 'public');
            }

            // Handle medical files
            if ($request->hasFile('medical_files')) {
                $existingFiles = $profile->medical_files ?? [];
                $newFiles = [];
                
                foreach ($request->file('medical_files') as $file) {
                    $newFiles[] = $file->store('profiles/medical-files', 'public');
                }
                
                $validated['medical_files'] = array_merge($existingFiles, $newFiles);
            }

            $profile->update($validated);

            return redirect()
                ->route('profiles.show', $profile)
                ->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified profile
     */
    public function destroy(Profile $profile)
    {
        try {
            // Delete profile image
            if ($profile->profile_image) {
                Storage::disk('public')->delete($profile->profile_image);
            }

            // Delete medical files
            if ($profile->medical_files) {
                foreach ($profile->medical_files as $file) {
                    Storage::disk('public')->delete($file);
                }
            }

            // Unlink all QR codes
            $profile->qrCodes()->detach();

            $profile->delete();

            return redirect()
                ->route('profiles.index')
                ->with('success', 'Profile deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete profile: ' . $e->getMessage());
        }
    }

    /**
     * Link QR code to profile
     */
    public function linkQrCode(Request $request, Profile $profile)
    {
        $request->validate([
            'qr_code' => 'required|string|exists:qr_codes,code',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $qrCode = QrCode::where('code', $request->qr_code)->firstOrFail();

            // Check if QR already has a profile
            if ($qrCode->hasProfile()) {
                return back()->with('error', 'This QR code is already linked to another profile.');
            }

            $profile->linkQrCode($qrCode->id, Auth::id(), $request->notes);

            return back()->with('success', 'QR code linked successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to link QR code: ' . $e->getMessage());
        }
    }

    /**
     * Unlink QR code from profile
     */
    public function unlinkQrCode(Profile $profile, QrCode $qrCode)
    {
        try {
            $profile->unlinkQrCode($qrCode->id);

            return response()->json([
                'success' => true,
                'message' => 'QR code unlinked successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unlink QR code: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show manage QR codes page for profile
     */
    public function manageQrCodes(Profile $profile)
    {
        $profile->load('qrCodes');
        
        $availableQrCodes = QrCode::active()
            ->withoutProfile()
            ->whereDoesntHave('orderItems')
            ->get();

        return view('profiles.manage-qr-codes', compact('profile', 'availableQrCodes'));
    }

    /**
     * Delete medical file
     */
    public function deleteMedicalFile(Profile $profile, $fileIndex)
    {
        try {
            $medicalFiles = $profile->medical_files ?? [];
            
            if (isset($medicalFiles[$fileIndex])) {
                Storage::disk('public')->delete($medicalFiles[$fileIndex]);
                unset($medicalFiles[$fileIndex]);
                
                $profile->update(['medical_files' => array_values($medicalFiles)]);

                return response()->json([
                    'success' => true,
                    'message' => 'File deleted successfully!',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'File not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete file: ' . $e->getMessage(),
            ], 500);
        }
    }
}
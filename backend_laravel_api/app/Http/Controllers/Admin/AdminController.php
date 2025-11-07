<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PointsHistory;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalPoints = User::sum('points');
        $recentUsers = User::latest()->take(5)->get();
        $recentPointsActivity = PointsHistory::with(['user', 'admin'])
            ->latest()
            ->take(5)
            ->get();
        
        return view('admin.dashboard', compact('totalUsers', 'totalPoints', 'recentUsers', 'recentPointsActivity'));
    }

    public function users()
    {
        $users = User::paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function showUser($id)
    {
        $user = User::with('pointsHistory.admin')->findOrFail($id);
        
        // Generate QR code for the user
        $qrCodeService = new QrCodeService();
        $qrCodeData = $qrCodeService->generateQrCodeData($user->id, $user->email, $user->username ?? $user->full_name);
        $qrCodeImage = $qrCodeService->generateQrCodeImage($qrCodeData);
        $qrCodeBase64 = base64_encode($qrCodeImage);
        
        // Get points history
        $pointsHistory = $user->pointsHistory()
            ->with('admin')
            ->latest()
            ->paginate(10);
        
        return view('admin.users.show', compact('user', 'qrCodeBase64', 'qrCodeData', 'pointsHistory'));
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->full_name = $request->full_name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Show QR code scanner for awarding points
     */
    public function showQrScanner()
    {
        return view('admin.qr-scanner');
    }

    /**
     * Award points to user manually
     */
    public function awardPoints(Request $request, $id)
    {
        $request->validate([
            'points' => 'required|integer|min:1|max:1000',
            'description' => 'nullable|string|max:255'
        ]);

        $user = User::findOrFail($id);
        $points = $request->points;
        $description = $request->description ?? 'Points awarded manually by admin';

        $user->addPoints($points, auth()->id(), $description);

        return redirect()->back()->with('success', "Successfully awarded {$points} points to {$user->full_name}");
    }

    /**
     * Process QR code scan and award points
     */
    public function processQrScan(Request $request)
    {
        $request->validate([
            'qr_code_data' => 'required|string',
            'points' => 'required|integer|min:1|max:1000',
            'description' => 'nullable|string|max:255'
        ]);

        try {
            $qrCodeService = new QrCodeService();
            $qrData = $qrCodeService->decodeQrCodeData($request->qr_code_data);
            
            if (!$qrData || !isset($qrData['user_id'])) {
                return redirect()->back()->with('error', 'Invalid QR code data');
            }

            $user = User::find($qrData['user_id']);
            if (!$user) {
                return redirect()->back()->with('error', 'User not found');
            }

            $points = $request->points;
            $description = $request->description ?? 'Points awarded via QR code scan';
            
            $user->addPoints($points, auth()->id(), $description, $request->qr_code_data);

            return redirect()->back()->with('success', "Successfully awarded {$points} points to {$user->full_name}");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error processing QR code: ' . $e->getMessage());
        }
    }

    /**
     * Show all points history
     */
    public function pointsHistory()
    {
        $pointsHistory = PointsHistory::with(['user', 'admin'])
            ->latest()
            ->paginate(20);

        return view('admin.points-history', compact('pointsHistory'));
    }
}
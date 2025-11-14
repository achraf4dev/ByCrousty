<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PointsHistory;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PointsController extends Controller
{
    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Get user's points and recent history
     */
    public function getUserPoints(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $pointsHistory = PointsHistory::where('user_id', $user->id)
                ->with('admin:id,full_name')
                ->latest()
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => [
                    'total_points' => $user->points ?? 0,
                    'points_this_month' => $user->points_this_month,
                    'history' => $pointsHistory
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving points data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Award points to user by scanning QR code (Admin only)
     */
    public function awardPointsByQrCode(Request $request): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'qr_code_data' => 'required|string',
                'points' => 'required|integer|min:1|max:1000',
                'description' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Decode QR code data
            $qrData = $this->qrCodeService->decodeQrCodeData($request->qr_code_data);
            
            if (!$qrData || !isset($qrData['user_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code data'
                ], 400);
            }

            // Find the user
            $user = User::find($qrData['user_id']);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Award points
            $points = $request->points;
            $description = $request->description ?? "Points awarded via QR code scan";
            
            $user->addPoints($points, $request->user()->id, $description, $request->qr_code_data);

            return response()->json([
                'success' => true,
                'message' => "Successfully awarded {$points} points to {$user->full_name}",
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->full_name,
                        'email' => $user->email,
                        'total_points' => $user->points
                    ],
                    'points_awarded' => $points,
                    'awarded_by' => $request->user()->full_name
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error awarding points',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Award points to user by ID (Admin only)
     */
    public function awardPointsToUser(Request $request, $userId): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'points' => 'required|integer|min:1|max:1000',
                'description' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::findOrFail($userId);
            $points = $request->points;
            $description = $request->description ?? "Points awarded manually by admin";

            $user->addPoints($points, $request->user()->id, $description);

            return response()->json([
                'success' => true,
                'message' => "Successfully awarded {$points} points to {$user->full_name}",
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->full_name,
                        'email' => $user->email,
                        'total_points' => $user->points
                    ],
                    'points_awarded' => $points,
                    'awarded_by' => $request->user()->full_name
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error awarding points',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's points history (Admin can see any user, users can see own)
     */
    public function getUserPointsHistory(Request $request, $userId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            
            // Check if user can access this history
            if (!$currentUser->is_admin && $currentUser->id != $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this user\'s points history'
                ], 403);
            }

            $user = User::findOrFail($userId);
            
            $pointsHistory = PointsHistory::where('user_id', $userId)
                ->with('admin:id,full_name')
                ->latest()
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->full_name,
                        'email' => $user->email,
                        'total_points' => $user->points ?? 0
                    ],
                    'history' => $pointsHistory
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving points history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all users with points (Admin only)
     */
    public function getAllUsersPoints(Request $request): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $users = User::select('id', 'full_name', 'email', 'points')
                ->orderBy('points', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving users points',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

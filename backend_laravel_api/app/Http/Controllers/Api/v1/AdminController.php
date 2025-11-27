<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminAction;
use App\Models\Order;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Get user info by QR code
     * GET /api/v1/admin/user-by-qr/{code}
     */
    public function getUserByQr($code): JsonResponse
    {
        try {
            // Decode QR code data
            $qrData = $this->qrCodeService->decodeQrCodeData($code);
            
            if (!$qrData || !isset($qrData['user_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code'
                ], 400);
            }

            $user = User::select('id', 'full_name', 'phone_number', 'points', 'email')
                ->find($qrData['user_id']);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'phone' => $user->phone_number,
                    'points' => $user->points ?? 0,
                    'email' => $user->email,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error decoding QR code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add points to user
     * POST /api/v1/admin/add-points
     */
    public function addPoints(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
                'points' => 'required|integer|min:1|max:10000',
                'remark' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::findOrFail($request->user_id);
            $admin = $request->user();

            // Update user points
            $user->increment('points', $request->points);

            // Store admin action
            AdminAction::create([
                'admin_id' => $admin->id,
                'user_id' => $user->id,
                'points_added' => $request->points,
                'remark' => $request->remark,
            ]);

            // Reload user to get updated points
            $user->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Points added successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'phone' => $user->phone_number,
                    'points' => $user->points,
                    'email' => $user->email,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding points',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all orders
     * GET /api/v1/admin/orders
     */
    public function getOrders(Request $request): JsonResponse
    {
        try {
            $query = Order::with('user:id,full_name,phone_number')
                ->orderBy('created_at', 'desc');

            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $orders = $query->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $orders
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update order status
     * POST /api/v1/admin/orders/{id}/status
     */
    public function updateOrderStatus(Request $request, $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,preparing,ready,completed,cancelled',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $order = Order::with('user:id,full_name,phone_number')->findOrFail($id);
            $order->status = $request->status;
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'data' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating order status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get admin dashboard summary
     * GET /api/v1/admin/summary
     */
    public function getSummary(Request $request): JsonResponse
    {
        try {
            $today = now()->startOfDay();

            // Last points actions (last 10)
            $lastPointsActions = AdminAction::with(['user:id,full_name', 'admin:id,full_name'])
                ->latest()
                ->take(10)
                ->get();

            // Last orders (last 10)
            $lastOrders = Order::with('user:id,full_name,phone_number')
                ->latest()
                ->take(10)
                ->get();

            // Today's totals
            $scansToday = AdminAction::whereDate('created_at', $today)->count();
            $pointsAddedToday = AdminAction::whereDate('created_at', $today)->sum('points_added');
            $ordersToday = Order::whereDate('created_at', $today)->count();
            $totalRevenueToday = Order::whereDate('created_at', $today)->sum('total_amount');

            return response()->json([
                'success' => true,
                'data' => [
                    'last_points_actions' => $lastPointsActions,
                    'last_orders' => $lastOrders,
                    'totals_today' => [
                        'scans' => $scansToday,
                        'points_added' => $pointsAddedToday,
                        'orders' => $ordersToday,
                        'total_revenue' => number_format($totalRevenueToday, 2, '.', ''),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get points history with search
     * GET /api/v1/admin/history/points
     */
    public function getPointsHistory(Request $request): JsonResponse
    {
        try {
            $query = AdminAction::with(['user:id,full_name,phone_number', 'admin:id,full_name'])
                ->orderBy('created_at', 'desc');

            // Search by user name or phone
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhere('phone_number', 'like', "%{$search}%");
                });
            }

            // Filter by date range if provided
            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $history = $query->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $history
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching points history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get orders history with search
     * GET /api/v1/admin/history/orders
     */
    public function getOrdersHistory(Request $request): JsonResponse
    {
        try {
            $query = Order::with('user:id,full_name,phone_number')
                ->orderBy('created_at', 'desc');

            // Search by order number, user name, or phone
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('full_name', 'like', "%{$search}%")
                                    ->orWhere('phone_number', 'like', "%{$search}%");
                      });
                });
            }

            // Filter by status
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            // Filter by date range
            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $history = $query->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $history
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching orders history',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

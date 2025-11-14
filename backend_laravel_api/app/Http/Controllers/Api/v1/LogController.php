<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoginLog;

class LogController extends Controller
{
    /**
     * Return login logs for a given user (authenticated user can only view their own logs).
     */
    public function index(Request $request, $userId)
    {
        $auth = $request->user();

        if (! $auth || $auth->id !== (int) $userId) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $logs = LoginLog::where('user_id', $userId)->orderBy('created_at', 'desc')->get();

        return response()->json(['logs' => $logs]);
    }
}

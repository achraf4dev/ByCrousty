<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordResetToken;
use App\Services\QrCodeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\LoginLog;

class AuthController extends Controller
{
    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $user = User::create([
            'full_name' => $data['full_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        // Generate unique QR code data for the user
        $qrCodeData = $this->qrCodeService->generateQrCodeData(
            $user->id,
            $user->email,
            $user->username
        );

        // Update user with QR code data
        $user->update(['qr_code_data' => $qrCodeData]);

        $token = $user->createToken('api-token')->plainTextToken;

        // create a temporary signed verification URL (expires in 60 minutes)
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        return response()->json([
            'user' => $user,
            'token' => $token,
            'verification_url' => $verificationUrl,
            'qr_code_url' => $user->qr_code_url,
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required_without:username|string', // email or username
            'username' => 'required_without:email|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // support sending either 'email' or 'username' as the login identifier
        $login = $data['email'] ?? $data['username'];

        $user = User::where('email', $login)->orWhere('username', $login)->first();

        $ip = $request->ip();
        $ua = $request->userAgent();

        if (! $user) {
            // log failed attempt (no user found)
            LoginLog::create([
                'user_id' => null,
                'email' => $data['email'],
                'ip_address' => $ip,
                'user_agent' => $ua,
                'success' => false,
                'message' => 'user_not_found',
            ]);

            return response()->json(['message' => 'invalid_credentials'], 401);
        }

        if (! Hash::check($data['password'], $user->password)) {
            // log failed attempt
            LoginLog::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $ip,
                'user_agent' => $ua,
                'success' => false,
                'message' => 'invalid_credentials',
            ]);

            return response()->json(['message' => 'invalid_credentials'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        // log successful login
        LoginLog::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'ip_address' => $ip,
            'user_agent' => $ua,
            'success' => true,
            'message' => 'login_successful',
        ]);

        return response()->json(['user' => $user, 'token' => $token]);
    }

    public function logout(Request $request)
    {
        $token = $request->user()?->currentAccessToken();
        if ($token) {
            $token->delete();
        }

        return response()->json(['message' => 'logged_out'], 200);
    }

    public function forgot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $user = User::where('email', $data['email'])->first();
        if (! $user) {
            // For security, still return success
            return response()->json(['message' => 'if_the_email_exists_a_reset_token_was_created'], 200);
        }

        $rawToken = Str::random(64);
        $hashed = Hash::make($rawToken);

        PasswordResetToken::updateOrCreate(
            ['email' => $user->email],
            ['token' => $hashed, 'created_at' => Carbon::now()]
        );

        // In a real app you would email $rawToken. We'll return it for testing/dev convenience.
        return response()->json(['message' => 'reset_token_created', 'token' => $rawToken]);
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $record = PasswordResetToken::where('email', $data['email'])->first();
        if (! $record || ! Hash::check($data['token'], $record->token)) {
            return response()->json(['message' => 'invalid_token_or_email'], 400);
        }

        // Check expiry - 60 minutes
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            return response()->json(['message' => 'token_expired'], 400);
        }

        $user = User::where('email', $data['email'])->first();
        if (! $user) {
            return response()->json(['message' => 'user_not_found'], 404);
        }

        $user->password = $data['password'];
        $user->save();

        PasswordResetToken::where('email', $data['email'])->delete();

        return response()->json(['message' => 'password_reset_successful']);
    }

    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->email))) {
            return response()->json(['message' => 'invalid_verification_link'], 403);
        }

        if ($user->email_verified_at) {
            return response()->json(['message' => 'already_verified']);
        }

        $user->email_verified_at = Carbon::now();
        $user->save();

        return response()->json(['message' => 'email_verified']);
    }

    public function getMyQrCode(Request $request)
    {
        $user = $request->user();
        
        if (!$user->qr_code_data) {
            return response()->json(['message' => 'QR code not found'], 404);
        }

        $qrCodeImage = $this->qrCodeService->generateQrCodeImage($user->qr_code_data);

        return response($qrCodeImage, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'inline; filename="my-qr-code.png"',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    public function getUserProfile(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'user' => $user,
            'qr_code_url' => $user->qr_code_url,
        ]);
    }
}

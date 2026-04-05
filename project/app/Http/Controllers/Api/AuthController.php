<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;
use App\Mail\VerificationCodeMail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed'
        ]);

        // Tạo mã xác thực 6 chữ số
        $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = Carbon::now()->addMinutes(15);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'verification_code' => $verificationCode,
            'verification_code_expires_at' => $expiresAt,
        ]);

        // Gửi email mã xác thực
        try {
            Mail::to($user->email)->send(new VerificationCodeMail($verificationCode, $user->name));
        } catch (\Exception $e) {
            // Log lỗi nhưng vẫn tạo user
            \Log::error('Failed to send verification email: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Đăng ký thành công! Vui lòng kiểm tra email để lấy mã xác thực.',
            'user_id' => $user->id,
            'email' => $user->email
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email không tồn tại trong hệ thống'
            ], 401);
        }

        if (!Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Mật khẩu không chính xác'
            ], 401);
        }

        // Kiểm tra email đã được xác thực chưa
        if (!$user->email_verified_at) {
            return response()->json([
                'message' => 'Email chưa được xác thực. Vui lòng kiểm tra email và nhập mã xác thực.',
                'requires_verification' => true,
                'user_id' => $user->id
            ], 403);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'Đăng nhập thành công'
        ]);
    }

    public function verifyEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6'
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email không tồn tại trong hệ thống'
            ], 404);
        }

        // Kiểm tra mã xác thực
        if (!$user->verification_code || $user->verification_code !== $validated['code']) {
            return response()->json([
                'message' => 'Mã xác thực không chính xác'
            ], 400);
        }

        // Kiểm tra mã còn hiệu lực không
        if ($user->verification_code_expires_at && $user->verification_code_expires_at->isPast()) {
            return response()->json([
                'message' => 'Mã xác thực đã hết hạn. Vui lòng yêu cầu mã mới.'
            ], 400);
        }

        // Xác thực email thành công
        $user->email_verified_at = Carbon::now();
        $user->verification_code = null;
        $user->verification_code_expires_at = null;
        $user->save();

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Xác thực email thành công!',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function resendVerificationCode(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email không tồn tại trong hệ thống'
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email đã được xác thực rồi'
            ], 400);
        }

        // Tạo mã mới
        $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = Carbon::now()->addMinutes(15);

        $user->verification_code = $verificationCode;
        $user->verification_code_expires_at = $expiresAt;
        $user->save();

        // Gửi email mã xác thực
        try {
            Mail::to($user->email)->send(new VerificationCodeMail($verificationCode, $user->name));
        } catch (\Exception $e) {
            \Log::error('Failed to send verification email: ' . $e->getMessage());
            return response()->json([
                'message' => 'Không thể gửi email. Vui lòng thử lại sau.'
            ], 500);
        }

        return response()->json([
            'message' => 'Đã gửi lại mã xác thực. Vui lòng kiểm tra email.'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function adminUsers(Request $request)
    {
        $currentUser = $request->user();

        if ($currentUser->role !== 'admin') {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $users = User::orderByDesc('id')->paginate(20);

        return response()->json($users);
    }

    public function facebookRedirect()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function facebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
            
            $user = User::where('email', $facebookUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $facebookUser->getName(),
                    'email' => $facebookUser->getEmail(),
                    'password' => Hash::make(uniqid()),
                    'role' => 'user'
                ]);
            }

            $token = $user->createToken('api_token')->plainTextToken;

            // Redirect to frontend with token
            $frontendUrl = env('FRONTEND_URL', request()->getSchemeAndHttpHost());
            return redirect($frontendUrl . '?token=' . $token . '&user=' . urlencode(json_encode($user)));
        } catch (\Exception $e) {
            $frontendUrl = env('FRONTEND_URL', request()->getSchemeAndHttpHost());
            return redirect($frontendUrl . '/auth?error=' . urlencode('Facebook login failed: ' . $e->getMessage()));
        }
    }

    public function facebookLogin(Request $request)
    {
        $validated = $request->validate([
            'access_token' => 'required|string'
        ]);

        try {
            $facebookUser = Socialite::driver('facebook')->userFromToken($validated['access_token']);
            
            $user = User::where('email', $facebookUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $facebookUser->getName(),
                    'email' => $facebookUser->getEmail(),
                    'password' => Hash::make(uniqid()),
                    'role' => 'user'
                ]);
            }

            $token = $user->createToken('api_token')->plainTextToken;

            return response()->json(['user' => $user, 'token' => $token]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Facebook login failed: ' . $e->getMessage()], 401);
        }
    }
}

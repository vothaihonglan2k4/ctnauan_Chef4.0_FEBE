<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Mail\EmailVerificationCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(RegisterRequest $request)
    {
        $verificationCode = rand(100000, 999999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => 'user',
            'email_verification_code' => $verificationCode,
            'email_verification_code_expires_at' => now()->addMinutes(5),
            'is_email_verified' => false,
        ]);

        Mail::to($user->email)->send(new EmailVerificationCode($user->name, $verificationCode));

        return response()->json([
            'message' => 'Đăng ký thành công. Vui lòng kiểm tra email để xác thực tài khoản.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->avatar
            ]
        ], 201);
    }

    /**
     * Login user
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email hoặc mật khẩu không đúng.']
            ]);
        }

        // Delete old tokens
        $user->tokens()->delete();

        $token = $user->createToken('auth-token')->plainTextToken;

        // Lấy permissions của user
        $permissions = [];
        if ($user->hasRole('admin')) {
            // Admin có full quyền
            $permissions = \App\Models\Permission::pluck('code')->toArray();
        } else {
            // Lấy quyền từ roles
            $permissions = $user->roles()
                ->with('permissions:code')
                ->get()
                ->pluck('permissions')
                ->flatten()
                ->pluck('code')
                ->unique()
                ->values()
                ->toArray();
        }

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->avatar,
                'permissions' => $permissions
            ],
            'token' => $token
        ]);
    }

    /**
     * Verify email with code
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Email không tồn tại.']
            ]);
        }

        if ($user->is_email_verified) {
            return response()->json([
                'message' => 'Email đã được xác thực trước đó.'
            ]);
        }

        if ($user->email_verification_code !== $request->code) {
            throw ValidationException::withMessages([
                'code' => ['Mã xác thực không đúng.']
            ]);
        }

        if ($user->email_verification_code_expires_at && now()->gt($user->email_verification_code_expires_at)) {
            throw ValidationException::withMessages([
                'code' => ['Mã xác thực đã hết hạn. Vui lòng gửi lại.']
            ]);
        }

        $user->update([
            'is_email_verified' => true,
            'email_verified_at' => now(),
            'email_verification_code' => null,
            'email_verification_code_expires_at' => null,
        ]);

        return response()->json([
            'message' => 'Xác thực email thành công.'
        ]);
    }

    /**
     * Resend verification code
     */
    public function resendVerificationCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Email không tồn tại.']
            ]);
        }

        if ($user->is_email_verified) {
            return response()->json([
                'message' => 'Email đã được xác thực.'
            ]);
        }

        $verificationCode = rand(100000, 999999);

        $user->update([
            'email_verification_code' => $verificationCode,
            'email_verification_code_expires_at' => now()->addMinutes(5),
        ]);

        Mail::to($user->email)->send(new EmailVerificationCode($user->name, $verificationCode));

        return response()->json([
            'message' => 'Đã gửi lại mã xác thực qua email.'
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Đăng xuất thành công'
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        $user = $request->user();

        // Lấy permissions của user
        $permissions = [];
        if ($user->hasRole('admin')) {
            // Admin có full quyền
            $permissions = \App\Models\Permission::pluck('code')->toArray();
        } else {
            // Lấy quyền từ roles
            $permissions = $user->roles()
                ->with('permissions:code')
                ->get()
                ->pluck('permissions')
                ->flatten()
                ->pluck('code')
                ->unique()
                ->values()
                ->toArray();
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->avatar,
                'phone' => $user->phone,
                'address' => $user->address,
                'created_at' => $user->created_at,
                'permissions' => $permissions
            ]
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $data = $request->only(['name', 'email', 'phone', 'address']);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = time() . '_' . $avatar->getClientOriginalName();
            
            // Create avatars directory if not exists
            $avatarPath = public_path('uploads/avatars');
            if (!file_exists($avatarPath)) {
                mkdir($avatarPath, 0777, true);
            }
            
            $avatar->move($avatarPath, $filename);
            $data['avatar'] = $filename;
            
            // Delete old avatar if exists and not default
            if ($user->avatar && $user->avatar !== 'default-avatar.png') {
                $oldAvatarPath = public_path('uploads/avatars/' . $user->avatar);
                if (file_exists($oldAvatarPath)) {
                    unlink($oldAvatarPath);
                }
            }
        }

        $user->update($data);

        return response()->json([
            'message' => 'Cập nhật thông tin thành công',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->avatar,
                'phone' => $user->phone,
                'address' => $user->address
            ]
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Mật khẩu hiện tại không đúng.']
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Delete all tokens to force re-login
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Đổi mật khẩu thành công. Vui lòng đăng nhập lại.'
        ]);
    }
}
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:191',
                'email' => 'required|email|max:191|unique:users,email',
                'password' => [
                    'required',
                    'min:6',
                    'regex:/^(?=.*[A-Z])(?=.*\W).+$/',
                ],
//                'google2fa_code' => 'required|digits:6',
            ]
        );

//        if ($validator->fails()) {
//            return response()->json(['errors' => $validator->messages()], Response::HTTP_BAD_REQUEST);
//        }
//
//        $google2fa = new Google2FA();
//        $secret = $request->input('google2fa_secret');
//
//        // Verify the 2FA code
//        $valid = $google2fa->verifyKey($secret, $request->google2fa_code);
//
//        if (!$valid) {
//            return response()->json(['message' => 'Mã Google Authenticator không chính xác!'], Response::HTTP_UNAUTHORIZED);
//        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
//            'google2fa_secret' => $secret,
        ]);

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Đăng ký thành công.',
            'username' => $user->name,
        ]);
    }
    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|max:191',
                'password' => [
                    'required',
                    'min:6',
                    'regex:/^(?=.*[A-Z])(?=.*\W).+$/',
                ],
            ],
            [
                'required'  => 'Bạn phải điền :attribute',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'validator_errors' => $validator->messages(),
            ]);
        } else {
            $user = User::where('email', $request->email)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => Response::HTTP_UNAUTHORIZED,
                    'message' => 'Thông tin không hợp lệ!',
                ]);
            } else {
                if ($user->role == 1) { // admin
                    $role = 'admin';
                    $token = $user->createToken($user->email . '_AdminToken', ['server:admin'])->plainTextToken;
                } else {
                    $role = '';
                    $token = $user->createToken($user->email . '_Token', [''])->plainTextToken;
                }
                return response()->json([
                    'status' => Response::HTTP_OK,
                    'username' => $user->name,
                    'token' => $token,
                    'message' => 'Đăng nhập thành công.',
                    'role' => $role,
                ]);
            }
        }
    }
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Đã đăng xuất.',
        ]);
    }
    public function getQrCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:191|unique:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], Response::HTTP_BAD_REQUEST);
        }

        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $QR_Image = $google2fa->getQRCodeUrl(
            config('app.name'), // Application name
            $request->email,
            $secret
        );

        return response()->json([
            'status' => Response::HTTP_OK,
            'qr_image' => $QR_Image, // URL mã QR để hiển thị trên frontend
            'google2fa_secret' => $secret,
        ]);
    }
}

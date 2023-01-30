<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helpers;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required',
            ]);

            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error(
                    ['message' => 'Unauthorized'],
                    'Authentication Failed',
                    500,
                );
            }

            $user = User::where('email', $request->email)->first();
            if (!Hash::check($request->password, $user->password, [])) {
                throw new Exception('Invalid Credentials');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            if ($user) {
                if ($user->role == 1) {
                    return ResponseFormatter::success(
                        [
                            'value' => 1,
                            'message' => 'You Login as Sales',
                            'access_token' => $tokenResult,
                            'tokent_type' => 'Bearer',
                            'user' => $user,
                        ],
                        'Authenticated',
                    );
                } else  if ($user->role == 2) {
                    return ResponseFormatter::success(
                        [
                            'value' => 2,
                            'message' => 'You Login as Markom',
                            'access_token' => $tokenResult,
                            'tokent_type' => 'Bearer',
                            'user' => $user,
                        ],
                        'Authenticated',
                    );
                } else  if ($user->role == 2) {
                    return ResponseFormatter::success(
                        [
                            'value' => 3,
                            'message' => 'You Login as Owner',
                            'access_token' => $tokenResult,
                            'tokent_type' => 'Bearer',
                            'user' => $user,
                        ],
                        'Authenticated',
                    );
                }
            } else {
                return ResponseFormatter::Success([
                    'message' => 'Something went error',

                ], 'Authentication Failed');
            }
        } catch (Exception $e) {
            return ResponseFormatter::error([
                'message' => 'Something went error',
                'error' => $e,
            ], 'Authentication Failed', 500);
        }
    }

    public function register(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|unique:users,email',
                'password' => 'required',
                'no_telp' => 'required',
                'project_code' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return ResponseFormatter::error([
                'message' => $messages,
            ], 'Register Failed', 500);
        }

        $prefix = 'USGR' . $request->input('project_code');
        $user = User::create([
            'user_code' => Helpers::autonumber('users', 'id', $prefix),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'no_telp' => $request->input('no_telp'),
            'address' => $request->input('address'),
            'is_sales' => $request->input('is_sales'),
            'is_active' => 1,
            'is_queue' => $request->input('is_sales'),
            'role' => $request->input('role'),
            'project_code' => $request->input('project_code'),
            'last_queue' => date('Y-m-d H:i:s'),
        ]);

        $tokenResult = $user->createToken('authToken')->plainTextToken;

        $userByEmail = User::where('email', $request->email)->first();

        if ($user) {
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $userByEmail,
            ], 'Successfully Registered');
        } else {
            return ResponseFormatter::error(
                ['message' => 'Something Wrong Went'],
                'Registered Failed',
                500
            );
        }
    }

    public function code(Request $request)
    {
        $projectCode = $request->input('project_code');
        $codeUser = $request->input('code_user');

        $check = Project::where('project_code', $projectCode)->first();

        if ($check->code_markom == $codeUser) {
            return ResponseFormatter::success(
                [
                    'value' => 1,
                    'message' => 'Markom Code Founded',
                    'project' => $check,
                ],
                'Successfully',
            );
        } else if ($check->code_sales == $codeUser) {
            return ResponseFormatter::success(
                [
                    'value' => 2,
                    'message' => 'Sales Code Founded',
                    'project' => $check,
                ],
                'Successfully',
            );
        } else if ($check->code_owner == $codeUser) {
            return ResponseFormatter::success(
                [
                    'value' => 3,
                    'message' => 'Owner Code Founded',
                    'project' => $check,
                ],
                'Successfully',
            );
        } else {
            return ResponseFormatter::error(
                [
                    'value' => 4,
                    'message' => 'Code Not Found',
                ],
                'Failed',
                404,
            );
        }
    }
}
